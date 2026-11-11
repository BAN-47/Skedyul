<?php

namespace Tests\Feature;

use App\Http\Middleware\AuditActivityMiddleware;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class SectionManagementTest extends TestCase
{
    private array $collegeIds;

    private array $programIds;

    private string $academicYearId;

    private string $semesterId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
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
            $table->string('college_name');
            $table->string('college_code');
        });
        Schema::create('department', function (Blueprint $table) {
            $table->uuid('dept_id')->primary();
            $table->uuid('dept_college_id');
            $table->string('dept_name');
            $table->string('dept_code');
        });
        Schema::create('dean', function (Blueprint $table) {
            $table->uuid('dean_id')->primary();
            $table->string('dean_usr_id');
            $table->uuid('dean_college_id');
            $table->string('dean_first_name')->nullable();
            $table->string('dean_middle_name')->nullable();
            $table->string('dean_last_name')->nullable();
        });
        Schema::create('academic_year', function (Blueprint $table) {
            $table->uuid('ay_id')->primary();
            $table->string('ay_academic_year');
            $table->boolean('ay_is_active')->default(false);
            $table->timestamp('ay_created_at')->nullable();
        });
        Schema::create('semester', function (Blueprint $table) {
            $table->uuid('sem_id')->primary();
            $table->uuid('sem_ay_id');
            $table->string('sem_name');
            $table->boolean('sem_is_active')->default(false);
        });
        Schema::create('section', function (Blueprint $table) {
            $table->uuid('sec_id')->primary();
            $table->uuid('sec_dept_id');
            $table->uuid('sec_ay_id');
            $table->uuid('sec_sem_id');
            $table->string('sec_name', 50);
            $table->integer('sec_year_level');
            $table->boolean('sec_is_evening')->default(false);
            $table->integer('sec_no_of_student')->default(0);
            $table->integer('sec_max_capacity')->default(40);
            $table->string('sec_status', 20)->default('active');
            $table->timestamp('sec_created_at')->nullable();
            $table->timestamp('sec_updated_at')->nullable();
        });
        Schema::create('study_load', function (Blueprint $table) {
            $table->uuid('sl_id')->primary();
            $table->uuid('sl_sec_id')->nullable();
        });
        Schema::create('schedule', function (Blueprint $table) {
            $table->uuid('sch_id')->primary();
            $table->uuid('sch_sec_id')->nullable();
        });
        Schema::create('section_course', function (Blueprint $table) {
            $table->uuid('ssub_id')->primary();
            $table->uuid('ssub_sec_id');
        });

        $this->collegeIds = [Str::uuid()->toString(), Str::uuid()->toString()];
        foreach ($this->collegeIds as $index => $collegeId) {
            DB::table('college')->insert([
                'college_id' => $collegeId,
                'college_name' => 'College '.($index + 1),
                'college_code' => 'C'.($index + 1),
            ]);
        }

        $this->programIds = [];
        foreach ($this->collegeIds as $index => $collegeId) {
            $programId = Str::uuid()->toString();
            $this->programIds[] = $programId;
            DB::table('department')->insert([
                'dept_id' => $programId,
                'dept_college_id' => $collegeId,
                'dept_name' => 'Program '.($index + 1),
                'dept_code' => 'P'.($index + 1),
            ]);
        }

        $this->academicYearId = Str::uuid()->toString();
        $this->semesterId = Str::uuid()->toString();
        DB::table('academic_year')->insert([
            'ay_id' => $this->academicYearId,
            'ay_academic_year' => '2026-2027',
            'ay_is_active' => true,
        ]);
        DB::table('semester')->insert([
            'sem_id' => $this->semesterId,
            'sem_ay_id' => $this->academicYearId,
            'sem_name' => 'First Semester',
            'sem_is_active' => true,
        ]);
    }

    public function test_admin_can_create_update_and_delete_a_section(): void
    {
        $admin = $this->user('admin', 'system_admin');

        $this->actingAs($admin)
            ->post(route('admin.sections.store'), $this->validSectionData())
            ->assertRedirect(route('admin.sections'))
            ->assertSessionHas('success', 'Section created successfully.');

        $sectionId = DB::table('section')->value('sec_id');
        $this->assertDatabaseHas('section', [
            'sec_id' => $sectionId,
            'sec_name' => 'BSIT-1A',
            'sec_no_of_student' => 0,
            'sec_is_evening' => true,
        ]);

        $this->put(route('admin.sections.update', $sectionId), [
            ...$this->validSectionData(),
            'sec_name' => 'BSIT-1B',
            'sec_is_evening' => '0',
        ])->assertRedirect(route('admin.sections'));

        $this->assertDatabaseHas('section', [
            'sec_id' => $sectionId,
            'sec_name' => 'BSIT-1B',
            'sec_is_evening' => false,
        ]);

        $this->delete(route('admin.sections.destroy', $sectionId))
            ->assertRedirect(route('admin.sections'));

        $this->assertDatabaseMissing('section', ['sec_id' => $sectionId]);
    }

    public function test_dean_only_sees_and_manages_sections_in_their_college(): void
    {
        $dean = $this->user('dean', 'dean');
        DB::table('dean')->insert([
            'dean_id' => Str::uuid()->toString(),
            'dean_usr_id' => $dean->usr_id,
            'dean_college_id' => $this->collegeIds[0],
        ]);

        $daySection = $this->sectionAttributes($this->programIds[0], 'Day Section');
        $daySection['sec_is_evening'] = false;
        DB::table('section')->insert([
            $this->sectionAttributes($this->programIds[0], 'My College Section'),
            $this->sectionAttributes($this->programIds[1], 'Other College Section'),
            $daySection,
        ]);

        $deanPage = $this->actingAs($dean)
            ->get(route('dean.sections'))
            ->assertOk()
            ->assertSee('My College Section')
            ->assertDontSee('Other College Section')
            ->assertSee('section-search', false)
            ->assertSee('section-view-modal', false)
            ->assertSee('function openModal', false);
        $this->assertSame(1, preg_match_all('/class="sidebar-logo"/', $deanPage->getContent()));

        $this->get(route('dean.sections', ['shift' => 'evening', 'year_level' => 1]))
            ->assertOk()
            ->assertSee('My College Section')
            ->assertDontSee('Day Section')
            ->assertDontSee('Other College Section');

        $this->post(route('dean.sections.store'), $this->validSectionData([
            'sec_dept_id' => $this->programIds[1],
            'sec_name' => 'Out of Scope',
        ]))->assertSessionHasErrors('sec_dept_id');

        $this->assertDatabaseMissing('section', ['sec_name' => 'Out of Scope']);

        $outsideSectionId = DB::table('section')
            ->where('sec_name', 'Other College Section')
            ->value('sec_id');
        $this->delete(route('dean.sections.destroy', $outsideSectionId))
            ->assertNotFound();
        $this->assertDatabaseHas('section', ['sec_id' => $outsideSectionId]);
    }

    public function test_admin_section_list_uses_client_pagination_and_filters(): void
    {
        $admin = $this->user('admin', 'system_admin');
        foreach (range(1, 12) as $number) {
            DB::table('section')->insert($this->sectionAttributes(
                $this->programIds[0],
                'Section '.$number
            ));
        }

        $response = $this->actingAs($admin)
            ->get(route('admin.sections'));

        $response->assertOk()
            ->assertSee('Filter sections')
            ->assertSee('section-pagination', false)
            ->assertSee('setupSectionPagination', false)
            ->assertSee('section-search', false)
            ->assertSee('applySearch', false)
            ->assertSee('function openModal', false)
            ->assertSee('section-view-modal', false)
            ->assertSee('Section 12');

        $this->assertSame(1, preg_match_all('/class="sidebar-logo"/', $response->getContent()));
        $response        ->assertSee('name="department"', false)
        ->assertSee('name="year_level"', false)
        ->assertSee('name="shift"', false)
        ->assertDontSee('name="academic_year"', false)
        ->assertDontSee('name="status"', false);
    }

    private function user(string $id, string $role): User
    {
        return User::create([
            'usr_id' => $id,
            'usr_name' => ucfirst($id),
            'usr_email' => $id.'@example.com',
            'usr_password_hash' => 'password-hash',
            'usr_role' => $role,
        ]);
    }

    private function validSectionData(array $overrides = []): array
    {
        return array_merge([
            'sec_dept_id' => $this->programIds[0],
            'sec_ay_id' => $this->academicYearId,
            'sec_sem_id' => $this->semesterId,
            'sec_name' => 'BSIT-1A',
            'sec_year_level' => 1,
            'sec_max_capacity' => 40,
            'sec_status' => 'active',
            'sec_is_evening' => '1',
        ], $overrides);
    }

    private function sectionAttributes(string $programId, string $name): array
    {
        return [
            'sec_id' => Str::uuid()->toString(),
            'sec_dept_id' => $programId,
            'sec_ay_id' => $this->academicYearId,
            'sec_sem_id' => $this->semesterId,
            'sec_name' => $name,
            'sec_year_level' => 1,
            'sec_is_evening' => true,
            'sec_no_of_student' => 0,
            'sec_max_capacity' => 40,
            'sec_status' => 'active',
        ];
    }
}
