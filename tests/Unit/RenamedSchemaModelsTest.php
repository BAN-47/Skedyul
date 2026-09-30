<?php

namespace Tests\Unit;

use App\Models\College;
use App\Models\Course;
use App\Models\Departments;
use App\Models\Schedule;
use App\Models\Section;
use App\Models\Study_Load;
use PHPUnit\Framework\TestCase;

class RenamedSchemaModelsTest extends TestCase
{
    public function test_models_use_the_renamed_tables_and_primary_keys(): void
    {
        $this->assertSame('college', (new College())->getTable());
        $this->assertSame('college_id', (new College())->getKeyName());
        $this->assertSame('department', (new Departments())->getTable());
        $this->assertSame('dept_id', (new Departments())->getKeyName());
        $this->assertSame('course', (new Course())->getTable());
        $this->assertSame('course_id', (new Course())->getKeyName());
    }

    public function test_legacy_form_keys_are_stored_in_renamed_columns(): void
    {
        $course = new Course([
            'subj_dept_id' => 'college-uuid',
            'subj_prog_id' => 'department-uuid',
            'subj_code' => 'CS101',
        ]);
        $section = new Section(['sec_prog_id' => 'department-uuid']);
        $studyLoad = new Study_Load(['sl_subj_id' => 'course-uuid']);
        $schedule = new Schedule(['sch_subj_id' => 'course-uuid']);

        $this->assertSame('college-uuid', $course->course_college_id);
        $this->assertSame('department-uuid', $course->course_dept_id);
        $this->assertSame('CS101', $course->course_code);
        $this->assertSame('department-uuid', $section->sec_dept_id);
        $this->assertSame('course-uuid', $studyLoad->sl_course_id);
        $this->assertSame('course-uuid', $schedule->sch_course_id);
    }
}