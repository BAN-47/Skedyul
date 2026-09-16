<?php

namespace App\Services;

use App\Models\Semester;
use App\Models\Study_Load;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ScheduleAssignmentService
{
    /**
     * Create a new Study_Load + Schedule pair.
     *
     * $data keys: subj_id, fac_id, sec_id, room_id, sch_day, sch_start_time, sch_end_time
     *
     * @return array{success:bool,status:int,message:string,conflict?:bool}
     */
    public function assign(array $data): array
    {
        $semester = Semester::where('sem_is_active', true)->first();
        if (!$semester) {
            return [
                'success' => false,
                'status'  => 422,
                'message' => 'No active semester is set. Ask the Technical Admin to activate one first.',
            ];
        }

        try {
            DB::beginTransaction();

            $studyLoad = Study_Load::create([
                'sl_fac_id'      => $data['fac_id'],
                'sl_subj_id'     => $data['subj_id'],
                'sl_sec_id'      => $data['sec_id'],
                'sl_sem_id'      => $semester->sem_id,
                'sl_assigned_by' => Auth::id(),
                'sl_status'      => 'draft',
            ]);

            Schedule::create([
                'sch_load_id'    => $studyLoad->sl_id,
                'sch_fac_id'     => $data['fac_id'],
                'sch_subj_id'    => $data['subj_id'],
                'sch_sec_id'     => $data['sec_id'],
                'sch_room_id'    => $data['room_id'],
                'sch_sem_id'     => $semester->sem_id,
                'sch_day'        => $data['sch_day'],
                'sch_start_time' => $data['sch_start_time'],
                'sch_end_time'   => $data['sch_end_time'],
                'sch_status'     => 'draft',
                'sch_created_by' => Auth::id(),
            ]);

            DB::commit();

            return [
                'success' => true,
                'status'  => 200,
                'message' => 'Subject assigned successfully.',
            ];
        } catch (QueryException $e) {
            DB::rollBack();
            return $this->handleConflict($e);
        }
    }

    /**
     * Update an existing Schedule row (and keep its paired Study_Load in sync).
     *
     * $data keys: same shape as assign().
     *
     * @return array{success:bool,status:int,message:string,conflict?:bool}
     */
    public function update(Schedule $schedule, array $data): array
    {
        try {
            DB::beginTransaction();

            $schedule->update([
                'sch_fac_id'     => $data['fac_id'],
                'sch_subj_id'    => $data['subj_id'],
                'sch_sec_id'     => $data['sec_id'],
                'sch_room_id'    => $data['room_id'],
                'sch_day'        => $data['sch_day'],
                'sch_start_time' => $data['sch_start_time'],
                'sch_end_time'   => $data['sch_end_time'],
            ]);

            $studyLoad = Study_Load::find($schedule->sch_load_id);
            if ($studyLoad) {
                $studyLoad->update([
                    'sl_fac_id'  => $data['fac_id'],
                    'sl_subj_id' => $data['subj_id'],
                    'sl_sec_id'  => $data['sec_id'],
                ]);
            }

            DB::commit();

            return [
                'success' => true,
                'status'  => 200,
                'message' => 'Schedule updated successfully.',
            ];
        } catch (QueryException $e) {
            DB::rollBack();
            return $this->handleConflict($e);
        }
    }

    /**
     * Postgres GiST exclusion constraint violation (SQLSTATE 23P01) means
     * exclude_room_overlap / exclude_faculty_overlap / exclude_section_overlap
     * fired because the time range collides with an existing row.
     */
    protected function handleConflict(QueryException $e): array
    {
        $isConflict = $e->getCode() === '23P01' || str_contains($e->getMessage(), 'exclude_');

        if ($isConflict) {
            return [
                'success'  => false,
                'status'   => 409,
                'conflict' => true,
                'message'  => 'Schedule Conflict! This faculty, room, or section is already booked at this time slot. Choose a different day or time.',
            ];
        }

        return [
            'success' => false,
            'status'  => 500,
            'message' => 'Failed to save. Please try again.',
        ];
    }
}