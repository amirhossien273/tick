<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_guest_can_view_login_page(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertViewIs('auth.login');
        $response->assertSee('خوش آمدید');
    }

    public function test_authenticated_user_is_redirected_from_login_to_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('login'));

        $response->assertRedirectToRoute('dashboard');
    }

    public function test_valid_credentials_authenticate_and_redirect_to_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => '123',
        ]);

        $response = $this->post(route('login.store'), [
            'email' => ' ADMIN@EXAMPLE.COM ',
            'password' => '123',
            'remember' => true,
        ]);

        $response->assertRedirectToRoute('dashboard');
        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->last_login_at);
    }

    public function test_inactive_user_cannot_authenticate(): void
    {
        User::factory()->inactive()->create([
            'email' => 'inactive@example.com',
            'password' => 'password',
        ]);

        $this->post(route('login.store'), [
            'email' => 'inactive@example.com',
            'password' => 'password',
        ])->assertSessionHasErrors([
            'email' => 'ایمیل یا رمز عبور واردشده صحیح نیست.',
        ]);

        $this->assertGuest();
    }

    public function test_invalid_credentials_return_visible_error_and_keep_user_logged_out(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => '123',
        ]);

        $response = $this->from(route('login'))->post(route('login.store'), [
            'email' => 'admin@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirectToRoute('login');
        $response->assertSessionHasErrors([
            'email' => 'ایمیل یا رمز عبور واردشده صحیح نیست.',
        ]);
        $this->assertGuest();
    }

    public function test_empty_credentials_return_persian_validation_messages(): void
    {
        $response = $this->from(route('login'))->post(route('login.store'));

        $response->assertRedirectToRoute('login');
        $response->assertSessionHasErrors([
            'email' => 'وارد کردن ایمیل الزامی است.',
            'password' => 'وارد کردن رمز عبور الزامی است.',
        ]);
    }

    public function test_login_is_rate_limited_after_five_failed_attempts(): void
    {
        User::factory()->create([
            'email' => 'limited@example.com',
            'password' => '123',
        ]);

        foreach (range(1, 5) as $attempt) {
            $this->post(route('login.store'), [
                'email' => 'limited@example.com',
                'password' => 'wrong-password-'.$attempt,
            ])->assertSessionHasErrors('email');
        }

        $this->post(route('login.store'), [
            'email' => 'limited@example.com',
            'password' => 'wrong-password',
        ])->assertTooManyRequests();
    }

    public function test_guest_is_redirected_from_dashboard_to_login(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirectToRoute('login');
    }

    public function test_authenticated_user_can_view_dashboard(): void
    {
        $user = User::factory()->create(['name' => 'مدیر سیستم']);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewIs('dashboard');
        $response->assertSee('مدیر سیستم');
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirectToRoute('login');
        $this->assertGuest();
    }
}
