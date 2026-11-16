<?php

namespace Tests\Feature;

use App\Http\Middleware\AuditActivityMiddleware;
use App\Models\User;
use Tests\TestCase;

class RoleRouteAccessTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(AuditActivityMiddleware::class);
    }

    public function test_dean_cannot_open_admin_chair_or_faculty_pages_directly(): void
    {
        $this->actingAs($this->user('dean'))
            ->get('/admin/users')
            ->assertForbidden();

        $this->actingAs($this->user('dean'))
            ->get('/chair/pbs')
            ->assertForbidden();

        $this->actingAs($this->user('dean'))
            ->get('/faculty/subjects')
            ->assertForbidden();
    }

    public function test_chair_cannot_open_admin_or_dean_pages_directly(): void
    {
        $this->actingAs($this->user('department_chair'))
            ->get('/admin/users')
            ->assertForbidden();

        $this->actingAs($this->user('department_chair'))
            ->get('/dean/dashboard')
            ->assertForbidden();
    }

    public function test_faculty_cannot_open_admin_chair_or_dean_pages_directly(): void
    {
        $this->actingAs($this->user('faculty'))
            ->get('/admin/users')
            ->assertForbidden();

        $this->actingAs($this->user('faculty'))
            ->get('/chair/pbs')
            ->assertForbidden();

        $this->actingAs($this->user('faculty'))
            ->get('/dean/dashboard')
            ->assertForbidden();
    }

    public function test_chair_settings_requires_a_signed_in_chair(): void
    {
        $this->get('/chair/settings')->assertRedirect(route('login'));

        $this->actingAs($this->user('dean'))
            ->get('/chair/settings')
            ->assertForbidden();
    }

    private function user(string $role): User
    {
        return new User([
            'usr_id' => 'test-' . $role,
            'usr_name' => ucfirst(str_replace('_', ' ', $role)),
            'usr_email' => $role . '@example.test',
            'usr_role' => $role,
            'usr_is_active' => true,
        ]);
    }
}
