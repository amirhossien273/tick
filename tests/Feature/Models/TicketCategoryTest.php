<?php

namespace Tests\Feature\Models;

use App\Models\TicketCategory;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class TicketCategoryTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_created_category_receives_uuid_and_casts_values(): void
    {
        $category = TicketCategory::factory()->inactive()->create([
            'sort_order' => 15,
        ])->refresh();

        $this->assertModelExists($category);
        $this->assertTrue(Str::isUuid($category->id));
        $this->assertSame(15, $category->sort_order);
        $this->assertFalse($category->is_active);
    }

    public function test_parent_and_children_relationships_preserve_child_when_parent_is_deleted(): void
    {
        $parent = TicketCategory::factory()->create();
        $child = TicketCategory::factory()->childOf($parent)->create();

        $this->assertTrue($child->parent->is($parent));
        $this->assertTrue($parent->children->contains($child));

        $parent->delete();

        $this->assertModelExists($child);
        $this->assertNull($child->refresh()->parent_id);
    }

    public function test_created_category_records_activity_changes(): void
    {
        $parent = TicketCategory::factory()->create();
        $category = TicketCategory::factory()->childOf($parent)->create([
            'name' => 'شبکه',
            'code' => 'network',
            'sort_order' => 30,
        ]);

        $activity = $category->activities()->where('event', 'created')->sole();
        $attributes = $activity->attribute_changes->get('attributes');

        $this->assertSame('ticket_categories', $activity->log_name);
        $this->assertTrue($activity->subject->is($category));
        $this->assertSame('شبکه', $attributes['name']);
        $this->assertSame('network', $attributes['code']);
        $this->assertSame($parent->id, $attributes['parent_id']);
    }
}
