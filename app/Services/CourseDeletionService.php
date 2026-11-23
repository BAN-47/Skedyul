<?php

namespace App\Services;

use App\Models\Course;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CourseDeletionService
{
    /** Permanently delete an unused course; keep courses needed by schedules/history. */
    public function deleteIfUnused(Course $course): bool
    {
        foreach ([
            'schedule' => 'sch_course_id',
            'study_load' => 'sl_course_id',
            'section_course' => 'ssub_subj_id',
            'schedule_archives' => 'sar_course_id',
        ] as $table => $column) {
            if (Schema::hasTable($table)
                && Schema::hasColumn($table, $column)
                && DB::table($table)->where($column, $course->course_id)->exists()) {
                return false;
            }
        }

        $course->delete();

        return true;
    }
}
