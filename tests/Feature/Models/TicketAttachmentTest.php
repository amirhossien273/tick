<?php

namespace Tests\Feature\Models;

use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class TicketAttachmentTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_created_attachment_exposes_relationships_casts_and_activity(): void
    {
        $ticket = Ticket::factory()->create();
        $uploader = User::factory()->create();
        $message = TicketMessage::factory()->fromAgent($uploader)->create([
            'ticket_id' => $ticket->id,
        ]);

        $attachment = TicketAttachment::factory()
            ->forMessage($message)
            ->uploadedBy($uploader)
            ->create([
                'original_filename' => 'invoice.pdf',
                'storage_disk' => 's3',
                'storage_path' => 'tickets/invoice.pdf',
                'mime_type' => 'application/pdf',
                'file_extension' => 'pdf',
                'file_size' => 2048,
            ])
            ->refresh();

        $activity = $attachment->activities()->where('event', 'created')->sole();
        $attributes = $activity->attribute_changes->get('attributes');

        $this->assertModelExists($attachment);
        $this->assertTrue(Str::isUuid($attachment->id));
        $this->assertTrue($attachment->ticket->is($ticket));
        $this->assertTrue($attachment->ticketMessage->is($message));
        $this->assertTrue($attachment->uploader->is($uploader));
        $this->assertSame(2048, $attachment->file_size);

        $this->assertSame('ticket_attachments', $activity->log_name);
        $this->assertTrue($activity->subject->is($attachment));
        $this->assertSame('invoice.pdf', $attributes['original_filename']);
        $this->assertSame('tickets/invoice.pdf', $attributes['storage_path']);
        $this->assertSame(2048, $attributes['file_size']);
    }

    public function test_deleting_message_preserves_attachment_and_clears_message_reference(): void
    {
        $message = TicketMessage::factory()->create();
        $attachment = TicketAttachment::factory()->forMessage($message)->create();

        $message->delete();

        $this->assertModelExists($attachment);
        $this->assertNull($attachment->refresh()->ticket_message_id);
    }

    public function test_deleting_uploader_preserves_attachment_and_clears_user_reference(): void
    {
        $uploader = User::factory()->create();
        $attachment = TicketAttachment::factory()->uploadedBy($uploader)->create();

        $uploader->delete();

        $this->assertModelExists($attachment);
        $this->assertNull($attachment->refresh()->uploaded_by_user_id);
    }

    public function test_ticket_exposes_attachments_and_deleting_ticket_removes_them(): void
    {
        $ticket = Ticket::factory()->create();
        $attachment = TicketAttachment::factory()->create([
            'ticket_id' => $ticket->id,
        ]);

        $this->assertTrue($ticket->attachments->contains($attachment));

        $ticket->delete();

        $this->assertModelMissing($attachment);
    }
}
