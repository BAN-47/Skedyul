<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\PbsSchedule;
use App\Models\Section;
use App\Models\Subjects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PbsController extends Controller
{
    /**
     * Display the PBS schedule page.
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'program',
            'year',
            'section',
            'semester',
        ]);

        $schedules = collect();

        /*
         * Only load schedules when a section is selected.
         */
        if (!empty($filters['section'])) {
            $schedules = PbsSchedule::query()
                ->where('pbs_sec_id', $filters['section'])
                ->when(
                    !empty($filters['semester']),
                    fn($q) => $q->where(
                        'pbs_sem_id',
                        $filters['semester']
                    )
                )
                ->where('pbs_is_active', true)
                ->with([
                    'subject',
                    'faculty',
                    'section',
                    'room',
                    'semester',
                ])
                ->orderBy('pbs_day')
                ->orderBy('pbs_start_time')
                ->get();
        }

        return view('chair.pbs', [
            'schedules' => $schedules,

            // Subjects
            'subjects' => Subjects::query()
                ->where('subj_is_active', true)
                ->orderBy('subj_code')
                ->get(),

            // Faculty
            'faculty' => Faculty::query()
                ->orderBy('fac_last_name')
                ->orderBy('fac_first_name')
                ->get(),

            // Sections
            'sections' => Section::query()
                ->when(
                    !empty($filters['program']),
                    fn($q) => $q->where(
                        'sec_prog_id',
                        $filters['program']
                    )
                )
                ->when(
                    !empty($filters['year']),
                    fn($q) => $q->where(
                        'sec_year_level',
                        $filters['year']
                    )
                )
                ->orderBy('sec_name')
                ->get(),

            // Programs
            'programs' => DB::table('program')
                ->orderBy('prog_name')
                ->get(),

            // Semesters
            'semesters' => DB::table('semester')
                ->orderBy('sem_start_date', 'desc')
                ->get(),

            // Rooms
            'rooms' => DB::table('room')
                ->where('room_is_available', true)
                ->orderBy('room_name')
                ->get(),

            'filters' => $filters,

            'selectedDate' => $request->query('date'),
        ]);
    }


    /**
     * Validate schedule input.
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'subj_id' => [
                'required',
                'uuid',
                'exists:subject,subj_id',
            ],

            'fac_id' => [
                'required',
                'uuid',
                'exists:faculty,fac_id',
            ],

            'sec_id' => [
                'required',
                'uuid',
                'exists:section,sec_id',
            ],

            'room_id' => [
                'required',
                'uuid',
                'exists:room,room_id',
            ],

            'sem_id' => [
                'required',
                'uuid',
                'exists:semester,sem_id',
            ],

            'day' => [
                'required',
                'string',
                'max:15',
            ],

            'start_time' => [
                'required',
                'date_format:H:i',
            ],

            'end_time' => [
                'required',
                'date_format:H:i',
                'after:start_time',
            ],

            'description' => [
                'nullable',
                'string',
            ],
        ]);
    }


    /**
     * Create a PBS schedule.
     */
    public function store(Request $request)
    {
        $data = $this->validated($request);

        /*
         * Check for an existing schedule for the same:
         * faculty + day + start time + semester.
         *
         * This matches the unique constraint in pbs_schedule.
         */
        $duplicate = PbsSchedule::query()
            ->where('pbs_fac_id', $data['fac_id'])
            ->where('pbs_day', $data['day'])
            ->where('pbs_start_time', $data['start_time'])
            ->where('pbs_sem_id', $data['sem_id'])
            ->exists();

        if ($duplicate) {
            return response()->json([
                'success' => false,
                'message' => 'This faculty member already has a schedule at this time for this semester.',
            ], 422);
        }

        /*
         * Create directly in pbs_schedule.
         */
        $schedule = PbsSchedule::create([
            'pbs_subj_id' => $data['subj_id'],
            'pbs_fac_id' => $data['fac_id'],
            'pbs_sec_id' => $data['sec_id'],
            'pbs_room_id' => $data['room_id'],
            'pbs_sem_id' => $data['sem_id'],

            'pbs_created_by' => auth()->id(),

            'pbs_day' => $data['day'],
            'pbs_start_time' => $data['start_time'],
            'pbs_end_time' => $data['end_time'],

            'pbs_description' => $data['description'] ?? null,

            'pbs_status' => 'draft',
            'pbs_is_active' => true,
        ]);

        /*
         * Load relationships for the response.
         */
        $schedule->load([
            'subject',
            'faculty',
            'section',
            'room',
            'semester',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'PBS schedule created successfully.',
            'schedule' => $schedule,
        ], 201);
    }


    /**
     * Update a PBS schedule.
     */
    public function update(Request $request, string $id)
    {
        $data = $this->validated($request);

        $schedule = PbsSchedule::find($id);

        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'PBS schedule not found.',
            ], 404);
        }

        /*
         * Check duplicate schedule, excluding the
         * schedule currently being edited.
         */
        $duplicate = PbsSchedule::query()
            ->where('pbs_fac_id', $data['fac_id'])
            ->where('pbs_day', $data['day'])
            ->where('pbs_start_time', $data['start_time'])
            ->where('pbs_sem_id', $data['sem_id'])
            ->where('pbs_id', '!=', $id)
            ->exists();

        if ($duplicate) {
            return response()->json([
                'success' => false,
                'message' => 'This faculty member already has a schedule at this time for this semester.',
            ], 422);
        }

        $schedule->update([
            'pbs_subj_id' => $data['subj_id'],
            'pbs_fac_id' => $data['fac_id'],
            'pbs_sec_id' => $data['sec_id'],
            'pbs_room_id' => $data['room_id'],
            'pbs_sem_id' => $data['sem_id'],

            'pbs_day' => $data['day'],
            'pbs_start_time' => $data['start_time'],
            'pbs_end_time' => $data['end_time'],

            'pbs_description' => $data['description'] ?? null,
        ]);

        $schedule->load([
            'subject',
            'faculty',
            'section',
            'room',
            'semester',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'PBS schedule updated successfully.',
            'schedule' => $schedule,
        ]);
    }


    /**
     * Delete a PBS schedule.
     */
    public function destroy(string $id)
    {
        $schedule = PbsSchedule::find($id);

        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'PBS schedule not found.',
            ], 404);
        }

        $schedule->delete();

        return response()->json([
            'success' => true,
            'message' => 'PBS schedule deleted successfully.',
        ]);
    }


    /**
     * Save draft.
     *
     * Schedules are already created with pbs_status = draft.
     */
    public function saveDraft(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Draft saved successfully.',
        ]);
    }


    /**
     * Clear all PBS schedules for a section.
     */
    public function clear(Request $request)
    {
        $sectionId = $request->input('section');

        if (!$sectionId) {
            return response()->json([
                'success' => false,
                'message' => 'No section selected.',
            ], 422);
        }

        $query = PbsSchedule::query()
            ->where('pbs_sec_id', $sectionId);

        if ($request->filled('semester')) {
            $query->where(
                'pbs_sem_id',
                $request->input('semester')
            );
        }

        $deleted = $query->delete();

        return response()->json([
            'success' => true,
            'message' => 'PBS schedules cleared successfully.',
            'deleted' => $deleted,
        ]);
    }
}
