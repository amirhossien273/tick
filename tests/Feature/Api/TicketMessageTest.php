<?php

namespace Tests\Feature\Api;

use App\Models\System;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class TicketMessageTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_customer_can_send_message_and_cannot_spoof_sender_fields(): void
    {
        $this->travelTo('2026-10-13 15:00:00');
        [$sourceSystem, $apiKey] = $this->createSourceSystem('a');
        $ticket = Ticket::factory()->for($sourceSystem, 'sourceSystem')->create([
            'external_user_id' => 'customer-88',
        ]);
        $injectedUser = User::factory()->create();

        $response = $this->withToken($apiKey)
            ->postJson(route('api.v1.tickets.messages.store', $ticket), [
                'message' => '  لطفاً وضعیت درخواست را بررسی کنید.  ',
                'sender_type' => 'agent',
                'user_id' => $injectedUser->getKey(),
                'external_user_id' => 'spoofed-customer',
                'is_internal_note' => true,
                'is_edited' => true,
            ]);

        $message = TicketMessage::query()->sole();
        $activity = $message->activities()->where('event', 'created')->sole();

        $response
            ->assertCreated()
            ->assertJsonPath('message', 'پیام با موفقیت ارسال شد.')
            ->assertJsonPath('data.id', $message->getKey())
            ->assertJsonPath('data.sender.type', 'customer')
            ->assertJsonPath('data.sender.external_user_id', 'customer-88')
            ->assertJsonPath('data.message', 'لطفاً وضعیت درخواست را بررسی کنید.');
        $this->assertTrue($message->ticket->is($ticket));
        $this->assertSame('customer', $message->sender_type);
        $this->assertSame('customer-88', $message->external_user_id);
        $this->assertNull($message->user_id);
        $this->assertFalse($message->is_internal_note);
        $this->assertFalse($message->is_edited);
        $this->assertSame('ticket_messages', $activity->log_name);
    }

    public function test_message_text_is_required(): void
    {
        [$sourceSystem, $apiKey] = $this->createSourceSystem('a');
        $ticket = Ticket::factory()->for($sourceSystem, 'sourceSystem')->create();

        $response = $this->withToken($apiKey)
            ->postJson(route('api.v1.tickets.messages.store', $ticket), [
                'message' => '   ',
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['message'])
            ->assertJsonPath('errors.message.0', 'متن پیام الزامی است.');
        $this->assertDatabaseCount('ticket_messages', 0);
    }

    public function test_conversation_is_chronological_and_hides_internal_notes(): void
    {
        [$sourceSystem, $apiKey] = $this->createSourceSystem('a');
        $ticket = Ticket::factory()->for($sourceSystem, 'sourceSystem')->create();
        $agent = User::factory()->create();
        $firstMessage = TicketMessage::factory()->for($ticket)->create([
            'message' => 'پیام اول مشتری',
            'created_at' => '2026-10-13 10:00:00',
        ]);
        $internalNote = TicketMessage::factory()->internalNote()->for($ticket)->create([
            'message' => 'یادداشت محرمانه کارشناس',
            'created_at' => '2026-10-13 10:30:00',
        ]);
        $secondMessage = TicketMessage::factory()->fromAgent($agent)->for($ticket)->create([
            'message' => 'پاسخ کارشناس',
            'created_at' => '2026-10-13 11:00:00',
        ]);

        $response = $this->withToken($apiKey)
            ->getJson(route('api.v1.tickets.messages.index', [
                'ticket' => $ticket,
                'per_page' => 10,
            ]));

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.id', $firstMessage->getKey())
            ->assertJsonPath('data.1.id', $secondMessage->getKey())
            ->assertJsonPath('data.1.sender.type', 'agent')
            ->assertJsonPath('data.1.sender.name', $agent->name)
            ->assertJsonPath('meta.total', 2)
            ->assertJsonMissing(['id' => $internalNote->getKey()])
            ->assertJsonMissing(['message' => 'یادداشت محرمانه کارشناس']);
    }

    public function test_messages_of_another_system_return_not_found(): void
    {
        [, $apiKey] = $this->createSourceSystem('a');
        [$otherSystem] = $this->createSourceSystem('b');
        $foreignTicket = Ticket::factory()->for($otherSystem, 'sourceSystem')->create();

        $this->withToken($apiKey)
            ->getJson(route('api.v1.tickets.messages.index', $foreignTicket))
            ->assertNotFound();

        $this->withToken($apiKey)
            ->postJson(route('api.v1.tickets.messages.store', $foreignTicket), [
                'message' => 'پیام غیرمجاز',
            ])
            ->assertNotFound();

        $this->assertDatabaseCount('ticket_messages', 0);
    }

    /** @return array{System, string} */
    private function createSourceSystem(string $keyCharacter): array
    {
        $apiKey = 'tkt_'.str_repeat($keyCharacter, 56);
        $sourceSystem = System::factory()->create();
        $sourceSystem->issueApiKey($apiKey)->saveQuietly();

        return [$sourceSystem, $apiKey];
    }
}
