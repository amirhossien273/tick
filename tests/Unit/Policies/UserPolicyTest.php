<?php

namespace Tests\Unit\Policies;

use App\Models\User;
use App\Policies\UserPolicy;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    public function test_active_admin_can_manage_users(): void
    {
        $admin = User::factory()->make(['is_admin' => true, 'is_active' => true]);
        $target = User::factory()->make();
        $policy = new UserPolicy;

        $this->assertTrue($policy->viewAny($admin));
        $this->assertTrue($policy->view($admin, $target));
        $this->assertTrue($policy->create($admin));
        $this->assertTrue($policy->update($admin, $target));
    }

    public function test_regular_or_inactive_user_cannot_manage_users(): void
    {
        $regularUser = User::factory()->make(['is_admin' => false, 'is_active' => true]);
        $inactiveAdmin = User::factory()->make(['is_admin' => true, 'is_active' => false]);
        $target = User::factory()->make();
        $policy = new UserPolicy;

        $this->assertFalse($policy->viewAny($regularUser));
        $this->assertFalse($policy->view($regularUser, $target));
        $this->assertFalse($policy->create($regularUser));
        $this->assertFalse($policy->update($regularUser, $target));
        $this->assertFalse($policy->viewAny($inactiveAdmin));
    }
}
