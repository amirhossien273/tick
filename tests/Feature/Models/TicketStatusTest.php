<?php

namespace Tests\Feature\Models;

use App\Models\TicketStatus;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class TicketStatusTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_created_status_receives_uuid_and_casts_values(): void
    {
        $status = TicketStatus::factory()->inactive()->create([
            'sort_order' => 15,
        ])->refresh();

        $this->assertModelExists($status);
        $this->assertTrue(Str::isUuid($status->id));
        $this->assertSame(15, $status->sort_order);
        $this->assertFalse($status->is_active);
    }

    public function test_created_status_records_activity_changes(): void
    {
        $status = TicketStatus::factory()->create([
            'name' => 'باز',
            'code' => 'open',
            'sort_order' => 20,
        ]);

        $activity = $status->activities()->where('event', 'created')->sole();
        $attributes = $activity->attribute_changes->get('attributes');

        $this->assertSame('ticket_statuses', $activity->log_name);
        $this->assertTrue($activity->subject->is($status));
        $this->assertSame('باز', $attributes['name']);
        $this->assertSame('open', $attributes['code']);
        $this->assertSame(20, $attributes['sort_order']);
    }
}
