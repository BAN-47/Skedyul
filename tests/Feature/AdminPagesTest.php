<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminPagesTest extends TestCase
{
    public function test_admin_users_page_requires_authentication(): void
    {
        $this->get(route('admin.users'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_subjects_page_requires_authentication(): void
    {
        $this->get(route('subject.index'))
            ->assertRedirect(route('login'));
    }
}
