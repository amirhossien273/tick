<?php

namespace Tests\Feature\Database\Seeders;

use App\Models\TicketTag;
use Database\Seeders\TicketTagSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class TicketTagSeederTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_seeder_creates_expected_tags_and_is_idempotent(): void
    {
        $this->seed(TicketTagSeeder::class);
        $this->seed(TicketTagSeeder::class);

        $vipTag = TicketTag::query()->where('slug', 'vip-customer')->sole();

        $this->assertDatabaseCount('ticket_tags', 7);
        $this->assertSame('مشتری ویژه', $vipTag->name);
        $this->assertSame('#7C3AED', $vipTag->color);
        $this->assertTrue($vipTag->is_active);
    }
}
