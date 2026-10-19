<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\TicketAssignment;
use App\Models\TicketAttachment;
use App\Models\TicketMessage;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TicketManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('tickets.index'))->assertRedirectToRoute('login');
    }

    public function test_active_user_can_search_and_filter_ticket_workbench(): void
    {
        $user = User::factory()->create();
        $assignee = User::factory()->create();
        $status = TicketStatus::factory()->create(['name' => 'باز']);
        $priority = TicketPriority::factory()->create(['name' => 'فوری']);
        $matched = Ticket::factory()->create([
            'subject' => 'درخواست TARGET مشتری', 'status_id' => $status->id,
            'priority_id' => $priority->id, 'assigned_to' => $assignee->id,
        ]);
        $hidden = Ticket::factory()->create(['subject' => 'درخواست دیگر']);

        $this->actingAs($user)->get(route('tickets.index', [
            'search' => 'TARGET', 'status_id' => $status->id, 'priority_id' => $priority->id, 'assigned_to' => $assignee->id,
        ]))->assertOk()->assertViewIs('tickets.index')->assertSee($matched->subject)->assertDontSee($hidden->subject);
    }

    public function test_inactive_user_cannot_use_ticket_workbench(): void
    {
        $user = User::factory()->inactive()->create();

        $this->actingAs($user)->get(route('tickets.index'))->assertRedirectToRoute('login');
    }

    public function test_user_can_view_ticket_conversation_and_content_is_escaped(): void
    {
        $user = User::factory()->create();
        $dangerous = '<script>alert("xss")</script>';
        $ticket = Ticket::factory()->create(['subject' => $dangerous, 'description' => $dangerous]);
        TicketMessage::factory()->for($ticket)->create(['message' => $dangerous]);

        $this->actingAs($user)->get(route('tickets.show', $ticket))
            ->assertOk()->assertViewIs('tickets.show')->assertSee($dangerous)->assertDontSee($dangerous, false);
    }

    public function test_agent_can_add_public_reply_internal_note_and_attachment(): void
    {
        Storage::fake('local');
        $agent = User::factory()->create();
        $ticket = Ticket::factory()->create();

        $this->actingAs($agent)->post(route('tickets.messages.store', $ticket), [
            'message' => ' پاسخ قابل مشاهده ', 'is_internal_note' => '0',
            'attachments' => [UploadedFile::fake()->create('guide.pdf', 120, 'application/pdf')],
            'sender_type' => 'customer', 'user_id' => User::factory()->create()->id,
        ])->assertRedirectToRoute('tickets.show', $ticket)->assertSessionHas('success');

        $reply = TicketMessage::query()->where('ticket_id', $ticket->id)->sole();
        $attachment = TicketAttachment::query()->where('ticket_message_id', $reply->id)->sole();
        $this->assertSame('agent', $reply->sender_type);
        $this->assertSame('پاسخ قابل مشاهده', $reply->message);
        $this->assertFalse($reply->is_internal_note);
        $this->assertTrue($reply->user->is($agent));
        Storage::disk('local')->assertExists($attachment->storage_path);
        $this->assertDatabaseHas('activity_log', ['subject_type' => TicketMessage::class, 'subject_id' => $reply->id]);
        $this->assertDatabaseHas('activity_log', ['subject_type' => TicketAttachment::class, 'subject_id' => $attachment->id]);

        $this->actingAs($agent)->post(route('tickets.messages.store', $ticket), [
            'message' => 'فقط برای تیم', 'is_internal_note' => '1',
        ])->assertRedirect();
        $this->assertDatabaseHas('ticket_messages', ['ticket_id' => $ticket->id, 'message' => 'فقط برای تیم', 'is_internal_note' => true]);
    }

    public function test_reply_requires_message_and_rejects_unsafe_file_type(): void
    {
        Storage::fake('local');
        $agent = User::factory()->create();
        $ticket = Ticket::factory()->create();

        $this->actingAs($agent)->post(route('tickets.messages.store', $ticket), [
            'message' => '', 'is_internal_note' => '0',
            'attachments' => [UploadedFile::fake()->create('payload.exe', 10, 'application/octet-stream')],
        ])->assertSessionHasErrors(['message', 'attachments.0']);
    }

    public function test_agent_can_change_status_and_resolution_timestamps_are_managed(): void
    {
        $agent = User::factory()->create();
        $open = TicketStatus::factory()->create(['code' => 'open']);
        $resolved = TicketStatus::factory()->create(['code' => 'resolved']);
        $ticket = Ticket::factory()->create(['status_id' => $open->id]);

        $this->actingAs($agent)->patch(route('tickets.status.update', $ticket), [
            'status_id' => $resolved->id, 'reason' => 'رفع مشکل',
        ])->assertRedirect()->assertSessionHas('success');

        $ticket->refresh();
        $this->assertTrue($ticket->status->is($resolved));
        $this->assertNotNull($ticket->resolved_at);
        $this->assertDatabaseHas('ticket_status_histories', [
            'ticket_id' => $ticket->id, 'from_status_id' => $open->id, 'to_status_id' => $resolved->id,
            'changed_by' => $agent->id, 'reason' => 'رفع مشکل',
        ]);

        $this->actingAs($agent)->patch(route('tickets.status.update', $ticket), ['status_id' => $open->id])->assertRedirect();
        $this->assertNull($ticket->fresh()->resolved_at);
    }

    public function test_agent_can_change_priority(): void
    {
        $agent = User::factory()->create();
        $oldPriority = TicketPriority::factory()->create();
        $newPriority = TicketPriority::factory()->create();
        $ticket = Ticket::factory()->create(['priority_id' => $oldPriority->id]);

        $this->actingAs($agent)->patch(route('tickets.priority.update', $ticket), ['priority_id' => $newPriority->id])
            ->assertRedirect()->assertSessionHas('success');
        $this->assertTrue($ticket->fresh()->priority->is($newPriority));
    }

    public function test_agent_can_transfer_and_unassign_ticket_with_history(): void
    {
        $actor = User::factory()->create();
        $firstAgent = User::factory()->create();
        $secondAgent = User::factory()->create();
        $ticket = Ticket::factory()->create(['assigned_to' => $firstAgent->id]);
        $oldAssignment = TicketAssignment::factory()->for($ticket)->create(['assigned_to' => $firstAgent->id]);

        $this->actingAs($actor)->patch(route('tickets.assignment.update', $ticket), [
            'assigned_to' => $secondAgent->id, 'reason' => 'انتقال تخصصی',
        ])->assertRedirect()->assertSessionHas('success');

        $this->assertNotNull($oldAssignment->fresh()->unassigned_at);
        $this->assertSame($secondAgent->id, $ticket->fresh()->assigned_to);
        $this->assertDatabaseHas('ticket_assignments', [
            'ticket_id' => $ticket->id, 'assigned_to' => $secondAgent->id, 'assigned_by' => $actor->id, 'reason' => 'انتقال تخصصی',
        ]);

        $this->actingAs($actor)->patch(route('tickets.assignment.update', $ticket), ['assigned_to' => ''])->assertRedirect();
        $this->assertNull($ticket->fresh()->assigned_to);
        $this->assertNotNull(TicketAssignment::query()->where('assigned_to', $secondAgent->id)->sole()->unassigned_at);
    }

    public function test_attachment_download_is_authorized_scoped_and_requires_existing_file(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create();
        $otherTicket = Ticket::factory()->create();
        $attachment = TicketAttachment::factory()->for($ticket)->create([
            'storage_path' => 'ticket-attachments/report.pdf', 'original_filename' => 'report.pdf',
        ]);
        Storage::disk('local')->put($attachment->storage_path, 'pdf-content');

        $this->actingAs($user)->get(route('tickets.attachments.download', [$ticket, $attachment]))
            ->assertOk()->assertDownload('report.pdf');
        $this->actingAs($user)->get(route('tickets.attachments.download', [$otherTicket, $attachment]))->assertNotFound();
        Storage::disk('local')->delete($attachment->storage_path);
        $this->actingAs($user)->get(route('tickets.attachments.download', [$ticket, $attachment]))->assertNotFound();
    }
}
