<?php

namespace Tests\Feature\Database\Seeders;

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserSeederTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_seeder_creates_admin_with_hashed_password_and_is_idempotent(): void
    {
        $this->seed(AdminUserSeeder::class);
        $this->seed(AdminUserSeeder::class);

        $admin = User::query()->where('email', 'admin@example.com')->sole();

        $this->assertDatabaseCount('users', 1);
        $this->assertSame('مدیر سیستم', $admin->name);
        $this->assertTrue(Hash::check('123', $admin->password));
        $this->assertNotSame('123', $admin->password);
    }
}
