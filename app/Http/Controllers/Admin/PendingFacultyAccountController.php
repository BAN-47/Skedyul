<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ApprovalNotificationMail;
use App\Models\Faculty;
use App\Models\FacultyAccountReview;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class PendingFacultyAccountController extends Controller
{
    public function index()
    {
        $this->ensureSystemAdmin();

        $pendingAccounts = FacultyAccountReview::with([
            'user.faculty.college',
            'user.faculty.program',
        ])
            ->where('fvr_status', 'pending')
            ->orderBy('created_at')
            ->get();

        $rejectedAccounts = FacultyAccountReview::with('reviewer')
            ->where('fvr_status', 'rejected')
            ->orderByDesc('fvr_reviewed_at')
            ->limit(50)
            ->get();

        $namePairs = $pendingAccounts
            ->map(function (FacultyAccountReview $review) {
                $faculty = $review->user?->faculty;

                return [
                    'first' => mb_strtolower(trim($faculty?->fac_first_name ?? '')),
                    'last' => mb_strtolower(trim($faculty?->fac_last_name ?? '')),
                ];
            })
            ->filter(fn (array $pair) => $pair['first'] !== '' && $pair['last'] !== '')
            ->unique(fn (array $pair) => $pair['first'] . '|' . $pair['last'])
            ->values();

        $existingFacultyNames = collect();
        if ($namePairs->isNotEmpty()) {
            $existingFacultyNames = Faculty::query()
                ->select(['fac_usr_id', 'fac_first_name', 'fac_last_name'])
                ->where(function ($query) use ($namePairs) {
                    foreach ($namePairs as $pair) {
                        $query->orWhere(function ($nameQuery) use ($pair) {
                            $nameQuery
                                ->whereRaw('LOWER(fac_first_name) = ?', [$pair['first']])
                                ->whereRaw('LOWER(fac_last_name) = ?', [$pair['last']]);
                        });
                    }
                })
                ->get();
        }

        foreach ($pendingAccounts as $review) {
            $faculty = $review->user?->faculty;
            $firstName = mb_strtolower(trim($faculty?->fac_first_name ?? ''));
            $lastName = mb_strtolower(trim($faculty?->fac_last_name ?? ''));

            $review->setAttribute('possible_name_match', $firstName !== '' && $lastName !== '' &&
                $existingFacultyNames->contains(function (Faculty $existingFaculty) use ($review, $firstName, $lastName) {
                    return $existingFaculty->fac_usr_id !== $review->fvr_usr_id
                        && mb_strtolower(trim($existingFaculty->fac_first_name ?? '')) === $firstName
                        && mb_strtolower(trim($existingFaculty->fac_last_name ?? '')) === $lastName;
                }));
        }

        return view('admin.pending_accounts', compact('pendingAccounts', 'rejectedAccounts'));
    }

    public function photo(string $id)
    {
        $this->ensureSystemAdmin();
        $review = FacultyAccountReview::findOrFail($id);

        $privateDisk = (string) config('filesystems.private_uploads_disk', 'local');
        abort_unless(Storage::disk($privateDisk)->exists($review->fvr_id_photo_path), 404);

        return Storage::disk($privateDisk)->response(
            $review->fvr_id_photo_path,
            null,
            ['Cache-Control' => 'private, no-store']
        );
    }

    public function approve(string $id)
    {
        $this->ensureSystemAdmin();

        $approvedUser = null;

        DB::transaction(function () use ($id, &$approvedUser) {
            $review = FacultyAccountReview::where('fvr_id', $id)
                ->lockForUpdate()
                ->firstOrFail();
            abort_unless($review->fvr_status === 'pending', 404);

            $user = User::where('usr_id', $review->fvr_usr_id)
                ->lockForUpdate()
                ->firstOrFail();
            abort_unless($user->usr_role === 'faculty', 404);

            $user->usr_is_active = true;
            $user->save();
            $approvedUser = $user;

            $review->fvr_status = 'approved';
            $review->fvr_reviewed_by = auth()->id();
            $review->fvr_reviewed_at = now();
            $review->save();

            try {
                $faculty = Faculty::where('fac_usr_id', $approvedUser->usr_id)->first();
                if ($faculty) {
                    $continuationUrl = URL::temporarySignedRoute('faculty.education.edit', now()->addDays(7), ['facultyId' => $faculty->fac_id]);
                    $name = trim(implode(' ', array_filter([
                        $faculty->fac_first_name,
                        $faculty->fac_middle_name,
                        $faculty->fac_last_name,
                        $faculty->fac_suffix,
                    ]))) ?: $approvedUser->usr_name;
                    Mail::to($approvedUser->usr_email)->send(new ApprovalNotificationMail($name, $approvedUser->usr_email, $continuationUrl));
                }
            } catch (\Throwable $exception) {
                Log::warning('Approval notification email failed.', ['user_id' => $approvedUser->usr_id, 'exception' => $exception->getMessage()]);
            }
        });

        return redirect()
            ->route('admin.pending-accounts')
            ->with('success', 'Faculty account approved. The faculty member can now sign in.');
    }

    public function reject(\Illuminate\Http\Request $request, string $id)
    {
        $this->ensureSystemAdmin();

        $data = $request->validate([
            'decision_note' => ['required', 'string', 'min:5', 'max:2000'],
        ], [
            'decision_note.required' => 'Enter a reason for rejecting this registration.',
            'decision_note.min' => 'The rejection reason must be at least 5 characters.',
        ]);

        $photoPath = null;

        $result = DB::transaction(function () use ($id, $data, &$photoPath) {
            $review = FacultyAccountReview::where('fvr_id', $id)
                ->lockForUpdate()
                ->first();

            if (! $review) {
                return ['ok' => false, 'reason' => 'review_not_found'];
            }

            if ($review->fvr_status !== 'pending') {
                return [
                    'ok' => false,
                    'reason' => 'review_not_pending',
                    'status' => $review->fvr_status,
                ];
            }

            $user = User::where('usr_id', $review->fvr_usr_id)
                ->lockForUpdate()
                ->first();

            if (! $user) {
                return ['ok' => false, 'reason' => 'linked_user_not_found'];
            }

            if ($user->usr_role !== 'faculty') {
                return ['ok' => false, 'reason' => 'linked_user_not_faculty'];
            }

            $faculty = Faculty::where('fac_usr_id', $user->usr_id)->first();
            $review->fvr_applicant_name = trim(implode(' ', array_filter([
                $faculty?->fac_first_name ?? $user->usr_first_name,
                $faculty?->fac_middle_name ?? $user->usr_middle_name,
                $faculty?->fac_last_name ?? $user->usr_last_name,
                $faculty?->fac_suffix ?? $user->usr_suffix,
            ]))) ?: ($user->usr_name ?? 'Unknown applicant');
            $review->fvr_applicant_email = $user->usr_email;
            $review->fvr_decision_note = trim($data['decision_note']);
            $review->fvr_status = 'rejected';
            $review->fvr_reviewed_by = auth()->id();
            $review->fvr_reviewed_at = now();
            $review->save();

            $photoPath = $review->fvr_id_photo_path;
            $faculty?->delete();
            $user->delete();

            return ['ok' => true];
        });

        if (! $result['ok']) {
            Log::warning('Faculty account rejection did not proceed.', [
                'review_id' => $id,
                'reason' => $result['reason'],
                'review_status' => $result['status'] ?? null,
            ]);

            $message = match ($result['reason']) {
                'review_not_found' => 'This application could not be found. Refresh Pending Accounts and check whether it has already been handled.',
                'review_not_pending' => 'This application is no longer pending. Refresh Pending Accounts to see its current status.',
                'linked_user_not_found' => 'The applicant account linked to this application could not be found. The rejection was not completed; check the application record or contact support.',
                'linked_user_not_faculty' => 'The linked account is not a faculty account, so this application was not rejected. Check the application record or contact support.',
                default => 'The application could not be rejected. Refresh Pending Accounts and try again.',
            };

            return redirect()
                ->route('admin.pending-accounts')
                ->with('error', $message);
        }

        if ($photoPath) {
            Storage::disk((string) config('filesystems.private_uploads_disk', 'local'))->delete($photoPath);
        }

        return redirect()
            ->route('admin.pending-accounts')
            ->with('success', 'Registration rejected. The user and faculty records were deleted, and the decision reason was saved in the rejection history.');
    }

    private function ensureSystemAdmin(): void
    {
        abort_unless(auth()->user()?->usr_role === 'system_admin', 403);
    }
}
