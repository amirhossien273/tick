<?php

namespace Tests\Feature;

use App\Models\System;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class SystemApiAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware('system.api-key')->get('/testing/system-api', function (Request $request) {
            $system = $request->attributes->get('authenticatedSystem');

            return response()->json([
                'system_id' => $system->getKey(),
                'system_code' => $system->code,
            ]);
        });
    }

    public function test_missing_or_invalid_api_key_is_rejected(): void
    {
        $this->getJson('/testing/system-api')
            ->assertUnauthorized()
            ->assertJson(['message' => 'کلید API معتبر نیست.']);

        $this->withHeader('X-API-Key', 'invalid-key')
            ->getJson('/testing/system-api')
            ->assertUnauthorized();
    }

    public function test_active_system_can_authenticate_and_last_usage_is_recorded(): void
    {
        $apiKey = 'tkt_valid-system-key';
        $system = System::factory()->create();
        $system->issueApiKey($apiKey)->saveQuietly();

        $this->withToken($apiKey)
            ->getJson('/testing/system-api')
            ->assertOk()
            ->assertJson([
                'system_id' => $system->getKey(),
                'system_code' => $system->code,
            ]);

        $this->assertNotNull($system->fresh()->last_used_at);
    }

    public function test_inactive_system_is_rejected_without_updating_last_usage(): void
    {
        $apiKey = 'tkt_inactive-system-key';
        $system = System::factory()->inactive()->create();
        $system->issueApiKey($apiKey)->saveQuietly();

        $this->withHeader('X-API-Key', $apiKey)
            ->getJson('/testing/system-api')
            ->assertUnauthorized();

        $this->assertNull($system->fresh()->last_used_at);
    }

    public function test_rotating_api_key_immediately_invalidates_previous_key(): void
    {
        $admin = User::factory()->admin()->create();
        $oldApiKey = 'tkt_previous-system-key';
        $system = System::factory()->for($admin, 'creator')->create();
        $system->issueApiKey($oldApiKey)->saveQuietly();

        $this->actingAs($admin)
            ->post(route('systems.api-key.store', $system))
            ->assertRedirect();

        $newApiKey = $system->fresh()->api_key;

        $this->withToken($oldApiKey)
            ->getJson('/testing/system-api')
            ->assertUnauthorized();

        $this->withToken($newApiKey)
            ->getJson('/testing/system-api')
            ->assertOk();
    }
}
