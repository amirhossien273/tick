<?php

namespace Tests\Feature\Models;

use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

class TicketMessageTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_created_agent_message_exposes_relationships_and_casts(): void
    {
        $ticket = Ticket::factory()->create();
        $agent = User::factory()->create();

        $message = TicketMessage::factory()
            ->fromAgent($agent)
            ->internalNote()
            ->create([
                'ticket_id' => $ticket->id,
                'is_edited' => true,
                'edited_at' => '2025-10-13 12:00:00',
            ])
            ->refresh();

        $this->assertModelExists($message);
        $this->assertTrue(Str::isUuid($message->id));
        $this->assertSame('agent', $message->sender_type);
        $this->assertTrue($message->ticket->is($ticket));
        $this->assertTrue($message->user->is($agent));
        $this->assertTrue($message->is_internal_note);
        $this->assertTrue($message->is_edited);
        $this->assertInstanceOf(Carbon::class, $message->edited_at);
    }

    public function test_ticket_exposes_messages_and_deleting_ticket_removes_them(): void
    {
        $ticket = Ticket::factory()->create();
        $message = TicketMessage::factory()->create([
            'ticket_id' => $ticket->id,
        ]);

        $this->assertTrue($ticket->messages->contains($message));

        $ticket->delete();

        $this->assertModelMissing($message);
    }

    public function test_deleting_agent_preserves_message_and_clears_user_reference(): void
    {
        $agent = User::factory()->create();
        $message = TicketMessage::factory()->fromAgent($agent)->create();

        $agent->delete();

        $this->assertModelExists($message);
        $this->assertNull($message->refresh()->user_id);
    }

    public function test_created_and_edited_message_records_activity_changes(): void
    {
        $message = TicketMessage::factory()->create([
            'message' => 'لطفاً اطلاعات بیشتری ارسال کنید.',
        ]);

        $createdActivity = $message->activities()->where('event', 'created')->sole();
        $createdAttributes = $createdActivity->attribute_changes->get('attributes');

        $message->update([
            'message' => 'لطفاً تصویر خطا را ارسال کنید.',
            'is_edited' => true,
            'edited_at' => '2025-10-13 12:00:00',
        ]);

        $updatedActivity = $message->activities()->where('event', 'updated')->sole();
        $updatedAttributes = $updatedActivity->attribute_changes->get('attributes');

        $this->assertSame('ticket_messages', $createdActivity->log_name);
        $this->assertTrue($createdActivity->subject->is($message));
        $this->assertSame('لطفاً اطلاعات بیشتری ارسال کنید.', $createdAttributes['message']);
        $this->assertFalse($createdAttributes['is_edited']);

        $this->assertSame('لطفاً تصویر خطا را ارسال کنید.', $updatedAttributes['message']);
        $this->assertTrue($updatedAttributes['is_edited']);
        $this->assertArrayHasKey('edited_at', $updatedAttributes);
    }
}
