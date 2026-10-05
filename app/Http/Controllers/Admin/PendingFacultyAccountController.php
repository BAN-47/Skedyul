<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\FacultyAccountReview;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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

        return view('admin.pending_accounts', compact('pendingAccounts'));
    }

    public function photo(string $id)
    {
        $this->ensureSystemAdmin();
        $review = FacultyAccountReview::findOrFail($id);

        abort_unless(Storage::disk('local')->exists($review->fvr_id_photo_path), 404);

        return Storage::disk('local')->response(
            $review->fvr_id_photo_path,
            null,
            ['Cache-Control' => 'private, no-store']
        );
    }

    public function approve(string $id)
    {
        $this->ensureSystemAdmin();

        DB::transaction(function () use ($id) {
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

            $review->fvr_status = 'approved';
            $review->fvr_reviewed_by = auth()->id();
            $review->fvr_reviewed_at = now();
            $review->save();
        });

        return redirect()
            ->route('admin.pending-accounts')
            ->with('success', 'Faculty account approved. The faculty member can now sign in.');
    }

    public function reject(string $id)
    {
        $this->ensureSystemAdmin();

        DB::transaction(function () use ($id) {
            $review = FacultyAccountReview::where('fvr_id', $id)
                ->lockForUpdate()
                ->firstOrFail();
            abort_unless($review->fvr_status === 'pending', 404);

            $review->fvr_status = 'rejected';
            $review->fvr_reviewed_by = auth()->id();
            $review->fvr_reviewed_at = now();
            $review->save();
        });

        return redirect()
            ->route('admin.pending-accounts')
            ->with('success', 'Registration rejected. Send the applicant an email separately if needed.');
    }

    private function ensureSystemAdmin(): void
    {
        abort_unless(auth()->user()?->usr_role === 'system_admin', 403);
    }
}
