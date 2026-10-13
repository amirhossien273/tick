<?php

namespace Tests\Feature\Models;

use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Models\TicketStatusHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

class TicketStatusHistoryTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_created_history_exposes_relationships_timestamp_and_activity(): void
    {
        $this->travelTo('2025-10-13 10:00:00');
        $ticket = Ticket::factory()->create();
        $fromStatus = TicketStatus::factory()->create();
        $toStatus = TicketStatus::factory()->create();
        $changer = User::factory()->create();

        $history = TicketStatusHistory::factory()
            ->fromStatus($fromStatus)
            ->changedBy($changer)
            ->create([
                'ticket_id' => $ticket->id,
                'to_status_id' => $toStatus->id,
                'reason' => 'اطلاعات تکمیل شد',
            ])
            ->refresh();

        $activity = $history->activities()->where('event', 'created')->sole();
        $attributes = $activity->attribute_changes->get('attributes');

        $this->assertModelExists($history);
        $this->assertTrue(Str::isUuid($history->id));
        $this->assertTrue($history->ticket->is($ticket));
        $this->assertTrue($history->fromStatus->is($fromStatus));
        $this->assertTrue($history->toStatus->is($toStatus));
        $this->assertTrue($history->changer->is($changer));
        $this->assertInstanceOf(Carbon::class, $history->created_at);
        $this->assertArrayNotHasKey('updated_at', $history->getAttributes());

        $this->assertSame('ticket_status_histories', $activity->log_name);
        $this->assertTrue($activity->subject->is($history));
        $this->assertSame($fromStatus->id, $attributes['from_status_id']);
        $this->assertSame($toStatus->id, $attributes['to_status_id']);
        $this->assertSame('اطلاعات تکمیل شد', $attributes['reason']);
    }

    public function test_initial_status_history_accepts_missing_previous_status(): void
    {
        $history = TicketStatusHistory::factory()->create();

        $this->assertModelExists($history);
        $this->assertNull($history->from_status_id);
        $this->assertNull($history->fromStatus);
    }

    public function test_deleting_changer_preserves_history_and_clears_user_reference(): void
    {
        $changer = User::factory()->create();
        $history = TicketStatusHistory::factory()->changedBy($changer)->create();

        $changer->delete();

        $this->assertModelExists($history);
        $this->assertNull($history->refresh()->changed_by);
    }

    public function test_ticket_exposes_status_histories_and_deleting_ticket_removes_them(): void
    {
        $ticket = Ticket::factory()->create();
        $history = TicketStatusHistory::factory()->create([
            'ticket_id' => $ticket->id,
        ]);

        $this->assertTrue($ticket->statusHistories->contains($history));

        $ticket->delete();

        $this->assertModelMissing($history);
    }
}
