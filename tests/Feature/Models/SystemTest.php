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

    public function test_created_systems_receive_sequential_codes_in_requested_format(): void
    {
        $this->travelTo('2025-10-13 10:00:00');

        $firstSystem = System::factory()->create();
        $secondSystem = System::factory()->create();

        $this->assertSame('SYS-20251310-0001', $firstSystem->code);
        $this->assertSame('SYS-20251310-0002', $secondSystem->code);
    }

    public function test_system_code_sequence_restarts_when_year_changes(): void
    {
        $this->travelTo('2025-12-31 23:59:00');
        $firstSystem = System::factory()->create();
        $secondSystem = System::factory()->create();

        $this->travelTo('2026-01-01 00:01:00');
        $newYearSystem = System::factory()->create();

        $this->assertSame('SYS-20253112-0001', $firstSystem->code);
        $this->assertSame('SYS-20253112-0002', $secondSystem->code);
        $this->assertSame('SYS-20260101-0001', $newYearSystem->code);
    }

    public function test_supplied_system_code_is_replaced_by_generated_code(): void
    {
        $this->travelTo('2025-10-13 10:00:00');

        $system = System::factory()->create(['code' => 'CUSTOM-CODE']);

        $this->assertSame('SYS-20251310-0001', $system->code);
    }

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
        $this->assertArrayHasKey('code', $activity->attribute_changes->get('attributes'));
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
