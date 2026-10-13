<?php

namespace Tests\Feature\Models;

use App\Models\TicketTag;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class TicketTagTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_created_tag_receives_uuid_casts_active_state_and_records_activity(): void
    {
        $tag = TicketTag::factory()->inactive()->create([
            'name' => 'نیازمند پیگیری',
            'slug' => 'follow-up',
            'color' => '#F59E0B',
        ])->refresh();

        $activity = $tag->activities()->where('event', 'created')->sole();
        $attributes = $activity->attribute_changes->get('attributes');

        $this->assertModelExists($tag);
        $this->assertTrue(Str::isUuid($tag->id));
        $this->assertFalse($tag->is_active);

        $this->assertSame('ticket_tags', $activity->log_name);
        $this->assertTrue($activity->subject->is($tag));
        $this->assertSame('نیازمند پیگیری', $attributes['name']);
        $this->assertSame('follow-up', $attributes['slug']);
        $this->assertSame('#F59E0B', $attributes['color']);
        $this->assertFalse($attributes['is_active']);
    }
}
