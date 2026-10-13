<?php

namespace Tests\Feature\Models;

use App\Models\System;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class SystemTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_created_system_has_uuid_creator_and_activity(): void
    {
        $creator = User::factory()->create();
        $system = System::factory()->create(['created_by' => $creator->id]);

        $activity = $system->activities()->where('event', 'created')->sole();

        $this->assertTrue(Str::isUuid($system->id));
        $this->assertTrue($system->creator->is($creator));
        $this->assertSame('systems', $activity->log_name);
        $this->assertTrue($activity->subject->is($system));
        $this->assertArrayHasKey('name', $activity->attribute_changes->get('attributes'));
        $this->assertArrayNotHasKey('api_key', $activity->attribute_changes->get('attributes'));
    }

    public function test_api_key_is_encrypted_hidden_and_never_logged(): void
    {
        $system = System::factory()->create(['api_key' => 'initial-secret']);

        $storedApiKey = DB::table('systems')->where('id', $system->id)->value('api_key');

        $this->assertNotSame('initial-secret', $storedApiKey);
        $this->assertSame('initial-secret', $system->api_key);
        $this->assertArrayNotHasKey('api_key', $system->toArray());

        Activity::query()->delete();

        $system->update(['api_key' => 'rotated-secret']);

        $this->assertSame(0, Activity::query()->count());
        $this->assertSame('rotated-secret', $system->fresh()->api_key);
    }
}
