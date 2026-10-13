<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('users.index'))
            ->assertRedirectToRoute('login');
    }

    public function test_non_admin_user_is_forbidden_from_user_management(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('users.index'))
            ->assertForbidden();
    }

    public function test_admin_can_search_filter_and_paginate_users(): void
    {
        $admin = User::factory()->admin()->create();
        $matchedUser = User::factory()->create([
            'name' => 'کاربر هدف',
            'email' => 'target@example.com',
        ]);
        $hiddenUser = User::factory()->inactive()->create([
            'name' => 'کاربر دیگر',
            'email' => 'other@example.com',
        ]);
        User::factory()->count(10)->create();

        $this->actingAs($admin)
            ->get(route('users.index', ['search' => 'target', 'status' => 'active']))
            ->assertOk()
            ->assertViewIs('users.index')
            ->assertSee($matchedUser->name)
            ->assertDontSee($hiddenUser->name);

        $this->actingAs($admin)
            ->get(route('users.index'))
            ->assertOk()
            ->assertViewHas('users', fn ($users): bool => $users->count() === 10 && $users->hasPages());
    }

    public function test_admin_can_create_user_without_assigning_admin_privileges(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('users.store'), [
            'name' => '  کاربر جدید  ',
            'email' => 'NEW.USER@EXAMPLE.COM ',
            'password' => 'StrongPass123',
            'password_confirmation' => 'StrongPass123',
            'is_active' => true,
            'is_admin' => true,
        ]);

        $user = User::query()->where('email', 'new.user@example.com')->sole();

        $response->assertRedirectToRoute('users.show', $user);
        $response->assertSessionHas('success', 'کاربر جدید با موفقیت ایجاد شد.');
        $this->assertSame('کاربر جدید', $user->name);
        $this->assertTrue($user->is_active);
        $this->assertFalse($user->is_admin);
        $this->assertTrue(Hash::check('StrongPass123', $user->password));
    }

    public function test_create_user_displays_persian_validation_messages(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->from(route('users.create'))
            ->post(route('users.store'), [
                'name' => '',
                'email' => 'invalid-email',
                'password' => 'short',
                'password_confirmation' => 'different',
                'is_active' => true,
            ])
            ->assertRedirect(route('users.create'))
            ->assertSessionHasErrors([
                'name' => 'نام کاربر را وارد کنید.',
                'email' => 'یک ایمیل معتبر وارد کنید.',
                'password' => 'رمز عبور باید حداقل ۸ کاراکتر باشد.',
            ]);
    }

    public function test_admin_can_view_and_update_user_details(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create([
            'name' => 'نام اولیه',
            'email' => 'initial@example.com',
        ]);

        $this->actingAs($admin)
            ->get(route('users.show', $user))
            ->assertOk()
            ->assertSee('نام اولیه')
            ->assertSee('initial@example.com');

        $response = $this->actingAs($admin)->put(route('users.update', $user), [
            'name' => 'نام ویرایش‌شده',
            'email' => 'updated@example.com',
        ]);

        $response->assertRedirectToRoute('users.show', $user);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'نام ویرایش‌شده',
            'email' => 'updated@example.com',
        ]);
    }

    public function test_admin_can_view_create_user_form(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('users.create'))
            ->assertOk()
            ->assertViewIs('users.create')
            ->assertSee('ثبت کاربر جدید');
    }

    public function test_admin_can_view_edit_user_form(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $this->actingAs($admin)
            ->get(route('users.edit', $user))
            ->assertOk()
            ->assertViewIs('users.edit')
            ->assertSee($user->name);
    }

    public function test_admin_can_view_reset_password_form(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $this->actingAs($admin)
            ->get(route('users.password.edit', $user))
            ->assertOk()
            ->assertViewIs('users.password')
            ->assertSee('بازنشانی رمز عبور');
    }

    public function test_admin_can_deactivate_user_and_inactive_user_cannot_log_in(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create([
            'email' => 'agent@example.com',
            'password' => 'StrongPass123',
        ]);

        $this->actingAs($admin)
            ->patch(route('users.status.update', $user), ['is_active' => false])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertFalse($user->fresh()->is_active);

        $this->post(route('logout'));

        $this->post(route('login.store'), [
            'email' => 'agent@example.com',
            'password' => 'StrongPass123',
        ])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_cannot_deactivate_own_account(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->from(route('users.show', $admin))
            ->patch(route('users.status.update', $admin), ['is_active' => false])
            ->assertRedirect(route('users.show', $admin))
            ->assertSessionHasErrors([
                'is_active' => 'برای جلوگیری از قطع دسترسی، نمی‌توانید حساب خودتان را غیرفعال کنید.',
            ]);

        $this->assertTrue($admin->fresh()->is_active);
    }

    public function test_admin_can_reset_user_password_and_action_is_logged(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create(['password' => 'OldPassword123']);

        $response = $this->actingAs($admin)->patch(route('users.password.update', $user), [
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ]);

        $response->assertRedirectToRoute('users.show', $user);
        $this->assertTrue(Hash::check('NewPassword123', $user->fresh()->password));
        $this->assertDatabaseHas('activity_log', [
            'subject_id' => $user->id,
            'causer_id' => $admin->id,
            'event' => 'password_reset',
        ]);
    }

    public function test_inactive_authenticated_user_is_logged_out_on_next_request(): void
    {
        $user = User::factory()->inactive()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirectToRoute('login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_user_name_is_escaped_in_management_views(): void
    {
        $admin = User::factory()->admin()->create();
        $dangerousName = '<script>alert("xss")</script>';
        User::factory()->create(['name' => $dangerousName]);

        $this->actingAs($admin)
            ->get(route('users.index'))
            ->assertOk()
            ->assertSee($dangerousName)
            ->assertDontSee($dangerousName, false);
    }
}
