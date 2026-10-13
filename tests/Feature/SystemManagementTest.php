<?php

namespace Tests\Feature;

use App\Models\System;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SystemManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('systems.index'))
            ->assertRedirectToRoute('login');
    }

    public function test_regular_user_is_forbidden_from_system_management(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('systems.index'))
            ->assertForbidden();
    }

    public function test_admin_can_search_filter_and_paginate_systems(): void
    {
        $admin = User::factory()->admin()->create();
        $matchedSystem = System::factory()->for($admin, 'creator')->create([
            'name' => 'سامانه TARGET',
        ]);
        $hiddenSystem = System::factory()->inactive()->for($admin, 'creator')->create([
            'name' => 'سامانه OTHER',
        ]);
        System::factory()->count(10)->for($admin, 'creator')->create();

        $this->actingAs($admin)
            ->get(route('systems.index', ['search' => 'TARGET', 'status' => 'active']))
            ->assertOk()
            ->assertViewIs('systems.index')
            ->assertSee($matchedSystem->name)
            ->assertDontSee($hiddenSystem->name);

        $this->actingAs($admin)
            ->get(route('systems.index'))
            ->assertOk()
            ->assertViewHas('systems', fn ($systems): bool => $systems->count() === 10 && $systems->hasPages());
    }

    public function test_admin_can_view_create_form(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('systems.create'))
            ->assertOk()
            ->assertViewIs('systems.create')
            ->assertSee('ثبت سامانه جدید');
    }

    public function test_admin_can_create_system_with_encrypted_generated_api_key(): void
    {
        $admin = User::factory()->admin()->create();
        $otherUser = User::factory()->create();
        $this->travelTo('2026-10-13 12:00:00');

        $response = $this->actingAs($admin)->post(route('systems.store'), [
            'name' => '  سامانه فروش  ',
            'code' => ' b2c-shop ',
            'description' => '  درگاه فروش مشتریان  ',
            'is_active' => true,
            'api_key' => 'injected-key',
            'created_by' => $otherUser->id,
        ]);

        $system = System::query()->where('name', 'سامانه فروش')->sole();
        $storedApiKey = DB::table('systems')->where('id', $system->id)->value('api_key');
        $storedApiKeyHash = DB::table('systems')->where('id', $system->id)->value('api_key_hash');

        $response->assertRedirectToRoute('systems.show', $system);
        $response->assertSessionHas('api_key_revealed', true);
        $this->assertSame('سامانه فروش', $system->name);
        $this->assertSame('SYS-20261310-0001', $system->code);
        $this->assertSame('درگاه فروش مشتریان', $system->description);
        $this->assertTrue($system->creator->is($admin));
        $this->assertStringStartsWith('tkt_', $system->api_key);
        $this->assertSame(60, mb_strlen($system->api_key));
        $this->assertNotSame('injected-key', $system->api_key);
        $this->assertNotSame($system->api_key, $storedApiKey);
        $this->assertSame(System::hashApiKey($system->api_key), $storedApiKeyHash);
    }

    public function test_create_system_displays_persian_validation_messages(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->from(route('systems.create'))
            ->post(route('systems.store'), [
                'name' => '',
                'code' => 'duplicate',
                'description' => str_repeat('الف', 2001),
                'is_active' => true,
            ])
            ->assertRedirect(route('systems.create'))
            ->assertSessionHasErrors([
                'name' => 'نام سامانه را وارد کنید.',
                'description' => 'توضیحات نمی‌تواند بیشتر از ۲۰۰۰ کاراکتر باشد.',
            ]);
    }

    public function test_admin_can_view_system_api_key_and_last_usage(): void
    {
        $admin = User::factory()->admin()->create();
        $this->travelTo('2026-10-01 12:30:00');
        $system = System::factory()->for($admin, 'creator')->create([
            'api_key' => 'visible-secret-key',
            'last_used_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('systems.show', $system))
            ->assertOk()
            ->assertViewIs('systems.show')
            ->assertSee('visible-secret-key')
            ->assertSee('2026/10/01 - 12:30');
    }

    public function test_admin_can_view_edit_form_and_update_system_without_changing_key(): void
    {
        $admin = User::factory()->admin()->create();
        $system = System::factory()->for($admin, 'creator')->create([
            'api_key' => 'original-secret',
        ]);
        $originalCode = $system->code;

        $this->actingAs($admin)
            ->get(route('systems.edit', $system))
            ->assertOk()
            ->assertViewIs('systems.edit')
            ->assertSee($system->name);

        $response = $this->actingAs($admin)->put(route('systems.update', $system), [
            'name' => 'سامانه ویرایش‌شده',
            'code' => 'new-code',
            'description' => 'توضیحات جدید',
            'api_key' => 'injected-replacement',
        ]);

        $response->assertRedirectToRoute('systems.show', $system);
        $this->assertSame($originalCode, $system->fresh()->code);
        $this->assertSame('سامانه ویرایش‌شده', $system->fresh()->name);
        $this->assertSame('original-secret', $system->fresh()->api_key);
    }

    public function test_admin_can_disable_and_enable_system_access(): void
    {
        $admin = User::factory()->admin()->create();
        $system = System::factory()->for($admin, 'creator')->create();

        $this->actingAs($admin)
            ->patch(route('systems.status.update', $system), ['is_active' => false])
            ->assertRedirect()
            ->assertSessionHas('success', 'دسترسی سامانه به API غیرفعال شد.');
        $this->assertFalse($system->fresh()->is_active);

        $this->actingAs($admin)
            ->patch(route('systems.status.update', $system), ['is_active' => true])
            ->assertRedirect()
            ->assertSessionHas('success', 'دسترسی سامانه به API فعال شد.');
        $this->assertTrue($system->fresh()->is_active);
    }

    public function test_admin_can_rotate_api_key_and_rotation_is_logged_without_secret(): void
    {
        $admin = User::factory()->admin()->create();
        $system = System::factory()->for($admin, 'creator')->create(['api_key' => 'old-secret']);

        $response = $this->actingAs($admin)->post(route('systems.api-key.store', $system));

        $newApiKey = $system->fresh()->api_key;
        $newApiKeyHash = $system->fresh()->getRawOriginal('api_key_hash');
        $activity = $system->activities()->where('event', 'api_key_rotated')->sole();

        $response->assertRedirect();
        $response->assertSessionHas('api_key_revealed', true);
        $this->assertNotSame('old-secret', $newApiKey);
        $this->assertSame(System::hashApiKey($newApiKey), $newApiKeyHash);
        $this->assertStringStartsWith('tkt_', $newApiKey);
        $this->assertTrue($activity->causer->is($admin));
        $this->assertStringNotContainsString($newApiKey, $activity->properties->toJson());
    }

    public function test_system_content_is_escaped_in_management_views(): void
    {
        $admin = User::factory()->admin()->create();
        $dangerousContent = '<script>alert("xss")</script>';
        System::factory()->for($admin, 'creator')->create([
            'name' => $dangerousContent,
            'description' => $dangerousContent,
        ]);

        $this->actingAs($admin)
            ->get(route('systems.index'))
            ->assertOk()
            ->assertSee($dangerousContent)
            ->assertDontSee($dangerousContent, false);
    }

    public function test_api_key_is_not_exposed_by_model_serialization(): void
    {
        $system = System::factory()->create(['api_key' => 'private-secret']);

        $this->assertArrayNotHasKey('api_key', $system->toArray());
        $this->assertArrayNotHasKey('api_key_hash', $system->toArray());
    }
}
