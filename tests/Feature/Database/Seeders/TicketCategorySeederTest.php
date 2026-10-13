<?php

namespace Tests\Feature\Database\Seeders;

use App\Models\TicketCategory;
use Database\Seeders\TicketCategorySeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class TicketCategorySeederTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_seeder_creates_hierarchy_without_duplicates(): void
    {
        $this->seed(TicketCategorySeeder::class);
        $this->seed(TicketCategorySeeder::class);

        $technical = TicketCategory::query()->where('code', 'technical')->firstOrFail();
        $technicalChildCodes = $technical->children()
            ->orderBy('sort_order')
            ->pluck('code')
            ->all();

        $this->assertDatabaseCount('ticket_categories', 11);
        $this->assertSame(['software', 'hardware', 'network'], $technicalChildCodes);
        $this->assertDatabaseHas('ticket_categories', [
            'name' => 'شبکه',
            'code' => 'network',
            'parent_id' => $technical->id,
            'sort_order' => 30,
            'is_active' => true,
        ]);
    }
}
