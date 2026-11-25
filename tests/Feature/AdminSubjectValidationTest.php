<?php

namespace Tests\Feature;

use App\Http\Middleware\AuditActivityMiddleware;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminSubjectValidationTest extends TestCase
{
    private string $collegeId;

    private string $departmentId;

    private string $courseId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(AuditActivityMiddleware::class);

        Schema::create('USER', function (Blueprint $table) {
            $table->string('usr_id')->primary();
            $table->string('usr_name');
            $table->string('usr_email');
            $table->string('usr_password_hash');
            $table->string('usr_role');
            $table->boolean('usr_is_active')->default(true);
        });
        Schema::create('college', function (Blueprint $table) {
            $table->uuid('college_id')->primary();
        });
        Schema::create('department', function (Blueprint $table) {
            $table->uuid('dept_id')->primary();
        });
        Schema::create('course', function (Blueprint $table) {
            $table->uuid('course_id')->primary();
            $table->string('course_code');
        });

        $this->collegeId = (string) Str::uuid();
        $this->departmentId = (string) Str::uuid();
        $this->courseId = (string) Str::uuid();
        DB::table('college')->insert(['college_id' => $this->collegeId]);
        DB::table('department')->insert(['dept_id' => $this->departmentId]);
        DB::table('course')->insert([
            'course_id' => $this->courseId,
            'course_code' => 'CC111',
        ]);
    }

    public function test_admin_cannot_create_a_subject_with_angle_brackets_in_its_code(): void
    {
        $this->actingAs($this->admin())
            ->from(route('subject.index'))
            ->post(route('subject.store'), $this->subjectData([
                'subj_code' => 'CC<111>',
            ]))
            ->assertSessionHasErrors('subj_code');
    }

    public function test_admin_cannot_update_a_subject_with_angle_brackets_in_its_code(): void
    {
        $this->actingAs($this->admin())
            ->from(route('subject.index'))
            ->put(route('subject.update', $this->courseId), [
                ...$this->subjectData([
                    'subj_code' => 'CC<112>',
                ]),
                '_subject_form' => 'edit',
                '_subject_id' => $this->courseId,
            ])
            ->assertSessionHasErrors('subj_code');
    }

    private function admin(): User
    {
        return User::create([
            'usr_id' => (string) Str::uuid(),
            'usr_name' => 'Test Admin',
            'usr_email' => 'admin@example.com',
            'usr_password_hash' => Hash::make('admin-password'),
            'usr_role' => 'system_admin',
            'usr_is_active' => true,
        ]);
    }

    private function subjectData(array $overrides = []): array
    {
        return array_merge([
            'subj_dept_id' => $this->collegeId,
            'subj_prog_id' => $this->departmentId,
            'subj_code' => 'CC112',
            'subj_name' => 'Computing',
            'subj_units' => 3,
            'subj_lecture_units' => 3,
            'subj_lab_units' => 0,
        ], $overrides);
    }
}
