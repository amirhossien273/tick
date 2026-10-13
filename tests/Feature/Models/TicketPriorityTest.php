<?php

namespace Tests\Feature\Models;

use App\Models\TicketPriority;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class TicketPriorityTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_created_priority_receives_uuid_and_casts_values(): void
    {
        $priority = TicketPriority::factory()->inactive()->create([
            'sort_order' => 15,
        ])->refresh();

        $this->assertModelExists($priority);
        $this->assertTrue(Str::isUuid($priority->id));
        $this->assertSame(15, $priority->sort_order);
        $this->assertFalse($priority->is_active);
    }

    public function test_created_priority_records_activity_changes(): void
    {
        $priority = TicketPriority::factory()->create([
            'name' => 'فوری',
            'code' => 'urgent',
            'sort_order' => 40,
        ]);

        $activity = $priority->activities()->where('event', 'created')->sole();
        $attributes = $activity->attribute_changes->get('attributes');

        $this->assertSame('ticket_priorities', $activity->log_name);
        $this->assertTrue($activity->subject->is($priority));
        $this->assertSame('فوری', $attributes['name']);
        $this->assertSame('urgent', $attributes['code']);
        $this->assertSame(40, $attributes['sort_order']);
    }
}
