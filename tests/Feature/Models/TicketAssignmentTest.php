<?php

namespace Tests\Feature\Models;

use App\Models\Ticket;
use App\Models\TicketAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

class TicketAssignmentTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_created_assignment_exposes_relationships_casts_and_activity(): void
    {
        $this->travelTo('2025-10-13 10:00:00');
        $ticket = Ticket::factory()->create();
        $assignee = User::factory()->create();
        $assigner = User::factory()->create();

        $assignment = TicketAssignment::factory()
            ->assignedBy($assigner)
            ->create([
                'ticket_id' => $ticket->id,
                'assigned_to' => $assignee->id,
                'assigned_at' => '2025-10-13 10:00:00',
                'reason' => 'انتقال به تیم فنی',
            ])
            ->refresh();

        $activity = $assignment->activities()->where('event', 'created')->sole();
        $attributes = $activity->attribute_changes->get('attributes');

        $this->assertModelExists($assignment);
        $this->assertTrue(Str::isUuid($assignment->id));
        $this->assertTrue($assignment->ticket->is($ticket));
        $this->assertTrue($assignment->assignee->is($assignee));
        $this->assertTrue($assignment->assigner->is($assigner));
        $this->assertInstanceOf(Carbon::class, $assignment->assigned_at);
        $this->assertNull($assignment->unassigned_at);

        $this->assertSame('ticket_assignments', $activity->log_name);
        $this->assertTrue($activity->subject->is($assignment));
        $this->assertSame($assignee->id, $attributes['assigned_to']);
        $this->assertSame('انتقال به تیم فنی', $attributes['reason']);
    }

    public function test_ending_assignment_records_updated_activity(): void
    {
        $assignment = TicketAssignment::factory()->create();

        $assignment->update([
            'unassigned_at' => '2025-10-13 12:00:00',
            'reason' => 'انتقال به کارشناس دیگر',
        ]);

        $activity = $assignment->activities()->where('event', 'updated')->sole();
        $attributes = $activity->attribute_changes->get('attributes');

        $this->assertInstanceOf(Carbon::class, $assignment->refresh()->unassigned_at);
        $this->assertSame('انتقال به کارشناس دیگر', $attributes['reason']);
        $this->assertArrayHasKey('unassigned_at', $attributes);
    }

    public function test_deleting_assigner_preserves_assignment_and_clears_assigner_reference(): void
    {
        $assigner = User::factory()->create();
        $assignment = TicketAssignment::factory()->assignedBy($assigner)->create();

        $assigner->delete();

        $this->assertModelExists($assignment);
        $this->assertNull($assignment->refresh()->assigned_by);
    }

    public function test_ticket_exposes_assignments_and_deleting_ticket_removes_them(): void
    {
        $ticket = Ticket::factory()->create();
        $assignment = TicketAssignment::factory()->create([
            'ticket_id' => $ticket->id,
        ]);

        $this->assertTrue($ticket->assignments->contains($assignment));

        $ticket->delete();

        $this->assertModelMissing($assignment);
    }
}
