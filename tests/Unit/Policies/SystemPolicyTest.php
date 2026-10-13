<?php

namespace Tests\Unit\Policies;

use App\Models\System;
use App\Models\User;
use App\Policies\SystemPolicy;
use Tests\TestCase;

class SystemPolicyTest extends TestCase
{
    public function test_active_admin_can_manage_systems(): void
    {
        $admin = User::factory()->make(['is_admin' => true, 'is_active' => true]);
        $system = new System;
        $policy = new SystemPolicy;

        $this->assertTrue($policy->viewAny($admin));
        $this->assertTrue($policy->view($admin, $system));
        $this->assertTrue($policy->create($admin));
        $this->assertTrue($policy->update($admin, $system));
    }

    public function test_regular_or_inactive_user_cannot_manage_systems(): void
    {
        $regularUser = User::factory()->make(['is_admin' => false, 'is_active' => true]);
        $inactiveAdmin = User::factory()->make(['is_admin' => true, 'is_active' => false]);
        $system = new System;
        $policy = new SystemPolicy;

        $this->assertFalse($policy->viewAny($regularUser));
        $this->assertFalse($policy->view($regularUser, $system));
        $this->assertFalse($policy->create($regularUser));
        $this->assertFalse($policy->update($regularUser, $system));
        $this->assertFalse($policy->viewAny($inactiveAdmin));
    }
}
