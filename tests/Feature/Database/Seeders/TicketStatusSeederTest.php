<?php

namespace Tests\Feature\Database\Seeders;

use App\Models\TicketStatus;
use Database\Seeders\TicketStatusSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class TicketStatusSeederTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_seeder_creates_complete_ordered_statuses_without_duplicates(): void
    {
        $this->seed(TicketStatusSeeder::class);
        $this->seed(TicketStatusSeeder::class);

        $codes = TicketStatus::query()
            ->orderBy('sort_order')
            ->pluck('code')
            ->all();

        $this->assertDatabaseCount('ticket_statuses', 7);
        $this->assertSame([
            'new',
            'open',
            'in_progress',
            'pending',
            'resolved',
            'closed',
            'cancelled',
        ], $codes);
        $this->assertDatabaseHas('ticket_statuses', [
            'name' => 'جدید',
            'code' => 'new',
            'sort_order' => 10,
            'is_active' => true,
        ]);
    }
}
