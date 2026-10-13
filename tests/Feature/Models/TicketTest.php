<?php

namespace Tests\Feature\Models;

use App\Models\System;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_created_tickets_receive_sequential_numbers_in_requested_format(): void
    {
        $this->travelTo('2025-10-13 10:00:00');

        $firstTicket = Ticket::factory()->create();
        $secondTicket = Ticket::factory()->create();

        $this->assertSame('TKT-20251310-0001', $firstTicket->ticket_number);
        $this->assertSame('TKT-20251310-0002', $secondTicket->ticket_number);
    }

    public function test_ticket_number_sequence_restarts_when_year_changes(): void
    {
        $this->travelTo('2025-12-31 23:59:00');
        $firstTicket = Ticket::factory()->create();
        $secondTicket = Ticket::factory()->create();

        $this->travelTo('2026-01-01 00:01:00');
        $newYearTicket = Ticket::factory()->create();

        $this->assertSame('TKT-20253112-0001', $firstTicket->ticket_number);
        $this->assertSame('TKT-20253112-0002', $secondTicket->ticket_number);
        $this->assertSame('TKT-20260101-0001', $newYearTicket->ticket_number);
    }

    public function test_ticket_exposes_related_models_and_datetime_casts(): void
    {
        $sourceSystem = System::factory()->create();
        $status = TicketStatus::factory()->create();
        $priority = TicketPriority::factory()->create();
        $category = TicketCategory::factory()->create();
        $assignee = User::factory()->create();

        $ticket = Ticket::factory()->create([
            'source_system_id' => $sourceSystem->id,
            'status_id' => $status->id,
            'priority_id' => $priority->id,
            'category_id' => $category->id,
            'assigned_to' => $assignee->id,
            'resolved_at' => '2025-10-13 12:00:00',
            'closed_at' => '2025-10-13 13:00:00',
        ])->refresh();

        $this->assertModelExists($ticket);
        $this->assertTrue(Str::isUuid($ticket->id));
        $this->assertTrue($ticket->sourceSystem->is($sourceSystem));
        $this->assertTrue($ticket->status->is($status));
        $this->assertTrue($ticket->priority->is($priority));
        $this->assertTrue($ticket->category->is($category));
        $this->assertTrue($ticket->assignee->is($assignee));
        $this->assertInstanceOf(Carbon::class, $ticket->resolved_at);
        $this->assertInstanceOf(Carbon::class, $ticket->closed_at);
    }

    public function test_supplied_ticket_number_is_replaced_by_generated_number(): void
    {
        $this->travelTo('2025-10-13 10:00:00');

        $ticket = Ticket::factory()->create([
            'ticket_number' => 'CUSTOM-NUMBER',
        ]);

        $this->assertSame('TKT-20251310-0001', $ticket->ticket_number);
    }

    public function test_created_and_updated_ticket_records_activity_changes(): void
    {
        $this->travelTo('2025-10-13 10:00:00');
        $ticket = Ticket::factory()->create([
            'subject' => 'اختلال در ورود',
            'description' => 'امکان ورود وجود ندارد.',
        ]);

        $createdActivity = $ticket->activities()->where('event', 'created')->sole();
        $createdAttributes = $createdActivity->attribute_changes->get('attributes');

        $ticket->update([
            'subject' => 'اختلال در ورود کاربران',
            'description' => 'مشکل برای چند کاربر مشاهده شده است.',
        ]);

        $updatedActivity = $ticket->activities()->where('event', 'updated')->sole();
        $updatedAttributes = $updatedActivity->attribute_changes->get('attributes');

        $this->assertSame('tickets', $createdActivity->log_name);
        $this->assertTrue($createdActivity->subject->is($ticket));
        $this->assertSame('TKT-20251310-0001', $createdAttributes['ticket_number']);
        $this->assertSame('اختلال در ورود', $createdAttributes['subject']);

        $this->assertSame('اختلال در ورود کاربران', $updatedAttributes['subject']);
        $this->assertSame('مشکل برای چند کاربر مشاهده شده است.', $updatedAttributes['description']);
    }
}
