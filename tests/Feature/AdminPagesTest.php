<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminPagesTest extends TestCase
{
    public function test_admin_users_page_renders(): void
    {
        $response = $this->get('/admin/users');

        $response->assertStatus(200);
        $response->assertSee('All User Accounts');
    }

    public function test_admin_subjects_page_renders(): void
    {
        $response = $this->get('/admin/subjects');

        $response->assertStatus(200);
        $response->assertSee('Subject Management');
    }
}
