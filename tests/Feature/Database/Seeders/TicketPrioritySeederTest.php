<?php

namespace Tests\Feature\Database\Seeders;

use App\Models\TicketPriority;
use Database\Seeders\TicketPrioritySeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class TicketPrioritySeederTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_seeder_creates_complete_ordered_priorities_without_duplicates(): void
    {
        $this->seed(TicketPrioritySeeder::class);
        $this->seed(TicketPrioritySeeder::class);

        $codes = TicketPriority::query()
            ->orderBy('sort_order')
            ->pluck('code')
            ->all();

        $this->assertDatabaseCount('ticket_priorities', 5);
        $this->assertSame([
            'low',
            'normal',
            'high',
            'urgent',
            'critical',
        ], $codes);
        $this->assertDatabaseHas('ticket_priorities', [
            'name' => 'عادی',
            'code' => 'normal',
            'sort_order' => 20,
            'is_active' => true,
        ]);
    }
}
