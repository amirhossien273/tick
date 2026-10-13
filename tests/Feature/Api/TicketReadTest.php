<?php

namespace Tests\Feature\Api;

use App\Models\System;
use App\Models\Ticket;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class TicketReadTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_ticket_list_requires_api_key(): void
    {
        $this->getJson(route('api.v1.tickets.index'))
            ->assertUnauthorized()
            ->assertExactJson(['message' => 'کلید API معتبر نیست.']);
    }

    public function test_list_returns_only_filtered_tickets_of_authenticated_system(): void
    {
        [$sourceSystem, $apiKey] = $this->createSourceSystem('a');
        [$otherSystem] = $this->createSourceSystem('b');
        $openStatus = TicketStatus::factory()->create(['code' => 'open']);
        $closedStatus = TicketStatus::factory()->create(['code' => 'closed']);
        $highPriority = TicketPriority::factory()->create(['code' => 'high']);
        $normalPriority = TicketPriority::factory()->create(['code' => 'normal']);
        $matchedTicket = Ticket::factory()->for($sourceSystem, 'sourceSystem')->create([
            'external_user_id' => 'customer-100',
            'external_ticket_id' => 'EXT-SEARCH-1',
            'subject' => 'اختلال مهم پرداخت',
            'status_id' => $openStatus->getKey(),
            'priority_id' => $highPriority->getKey(),
        ]);
        $filteredTicket = Ticket::factory()->for($sourceSystem, 'sourceSystem')->create([
            'external_user_id' => 'customer-100',
            'subject' => 'اختلال مهم پرداخت دوم',
            'status_id' => $closedStatus->getKey(),
            'priority_id' => $normalPriority->getKey(),
        ]);
        $foreignTicket = Ticket::factory()->for($otherSystem, 'sourceSystem')->create([
            'external_user_id' => 'customer-100',
            'subject' => 'اختلال مهم پرداخت خارجی',
            'status_id' => $openStatus->getKey(),
            'priority_id' => $highPriority->getKey(),
        ]);

        $response = $this->withToken($apiKey)->getJson(route('api.v1.tickets.index', [
            'search' => 'اختلال مهم',
            'external_user_id' => 'customer-100',
            'status_code' => 'OPEN',
            'priority_code' => 'HIGH',
            'per_page' => 1,
        ]));

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $matchedTicket->getKey())
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('meta.per_page', 1)
            ->assertJsonMissing(['id' => $filteredTicket->getKey()])
            ->assertJsonMissing(['id' => $foreignTicket->getKey()]);
    }

    public function test_invalid_list_filters_return_validation_errors(): void
    {
        [, $apiKey] = $this->createSourceSystem('a');

        $response = $this->withToken($apiKey)->getJson(route('api.v1.tickets.index', [
            'status_code' => 'unknown',
            'priority_code' => 'unknown',
            'per_page' => 101,
        ]));

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['status_code', 'priority_code', 'per_page'])
            ->assertJsonPath('errors.status_code.0', 'وضعیت انتخاب‌شده معتبر نیست.')
            ->assertJsonPath('errors.priority_code.0', 'اولویت انتخاب‌شده معتبر نیست.')
            ->assertJsonPath('errors.per_page.0', 'تعداد نتایج هر صفحه نمی‌تواند بیشتر از ۱۰۰ باشد.');
    }

    public function test_system_can_view_its_ticket_details(): void
    {
        [$sourceSystem, $apiKey] = $this->createSourceSystem('a');
        $ticket = Ticket::factory()->for($sourceSystem, 'sourceSystem')->create([
            'external_user_id' => 'customer-25',
            'subject' => 'جزئیات تیکت',
        ]);

        $this->withToken($apiKey)
            ->getJson(route('api.v1.tickets.show', $ticket))
            ->assertOk()
            ->assertJsonPath('data.id', $ticket->getKey())
            ->assertJsonPath('data.ticket_number', $ticket->ticket_number)
            ->assertJsonPath('data.external_user_id', 'customer-25')
            ->assertJsonPath('data.source_system.code', $sourceSystem->code);
    }

    public function test_ticket_of_another_system_returns_not_found(): void
    {
        [, $apiKey] = $this->createSourceSystem('a');
        [$otherSystem] = $this->createSourceSystem('b');
        $foreignTicket = Ticket::factory()->for($otherSystem, 'sourceSystem')->create();

        $this->withToken($apiKey)
            ->getJson(route('api.v1.tickets.show', $foreignTicket))
            ->assertNotFound();
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
