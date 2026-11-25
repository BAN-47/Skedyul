<?php

namespace Tests\Feature;

use App\Http\Middleware\AuditActivityMiddleware;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class DeanSettingsValidationTest extends TestCase
{
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

        $this->actingAs(User::create([
            'usr_id' => (string) Str::uuid(),
            'usr_name' => 'Test Dean',
            'usr_email' => 'dean@example.com',
            'usr_password_hash' => Hash::make('Current!Pass123'),
            'usr_role' => 'dean',
            'usr_is_active' => true,
        ]));
    }

    public function test_dean_personal_info_rejects_angle_brackets_with_the_custom_message(): void
    {
        $this->putJson(route('dean.profile.personal-info.update'), [
            'dean_first_name' => 'Dean<',
            'dean_last_name' => 'Person',
        ])
            ->assertUnprocessable()
            ->assertJsonPath('errors.dean_first_name.0', 'Angle brackets (< and >) are not allowed.');
    }

    public function test_dean_contact_email_rejects_angle_brackets_with_the_custom_message(): void
    {
        $this->putJson(route('dean.profile.contact.update'), [
            'usr_email' => 'dean<one@example.com',
        ])
            ->assertUnprocessable()
            ->assertJsonPath('errors.usr_email.0', 'Angle brackets (< and >) are not allowed.');
    }

    public function test_institution_email_and_name_reject_angle_brackets_with_the_custom_message(): void
    {
        $this->putJson(route('dean.institution.update'), [
            'inst_name' => 'School <Name>',
            'inst_contact_email' => 'school@example.com',
        ])
            ->assertUnprocessable()
            ->assertJsonPath('errors.inst_name.0', 'Angle brackets (< and >) are not allowed.');
    }

    public function test_dean_password_rejects_angle_brackets_with_the_custom_message(): void
    {
        $this->putJson(route('dean.profile.password.update'), [
            'current_password' => 'Current!Pass123',
            'new_password' => 'New!Password<>',
            'new_password_confirmation' => 'New!Password<>',
        ])
            ->assertUnprocessable()
            ->assertJsonPath('errors.new_password.0', 'Angle brackets (< and >) are not allowed.');
    }
}
