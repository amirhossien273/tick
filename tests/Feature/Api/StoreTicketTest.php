<?php

namespace Tests\Feature\Api;

use App\Models\System;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class StoreTicketTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_request_without_api_key_is_rejected_and_does_not_create_ticket(): void
    {
        $response = $this->postJson(route('api.v1.tickets.store'), [
            'external_user_id' => 'customer-10',
            'subject' => 'مشکل در پرداخت',
        ]);

        $response
            ->assertUnauthorized()
            ->assertExactJson(['message' => 'کلید API معتبر نیست.']);
        $this->assertDatabaseCount('tickets', 0);
    }

    public function test_inactive_source_system_is_rejected(): void
    {
        $apiKey = 'tkt_inactive-source-key';
        $sourceSystem = System::factory()->inactive()->create();
        $sourceSystem->issueApiKey($apiKey)->saveQuietly();

        $this->withToken($apiKey)
            ->postJson(route('api.v1.tickets.store'), [
                'external_user_id' => 'customer-10',
                'subject' => 'مشکل در پرداخت',
            ])
            ->assertUnauthorized()
            ->assertExactJson(['message' => 'کلید API معتبر نیست.']);

        $this->assertDatabaseCount('tickets', 0);
    }

    public function test_required_fields_are_validated_with_persian_messages(): void
    {
        [$sourceSystem, $apiKey] = $this->createSourceSystem();

        $response = $this->withToken($apiKey)
            ->postJson(route('api.v1.tickets.store'), []);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['external_user_id', 'subject'])
            ->assertJsonPath('errors.external_user_id.0', 'شناسه کاربر در سامانه مبدأ الزامی است.')
            ->assertJsonPath('errors.subject.0', 'عنوان تیکت الزامی است.');
        $this->assertDatabaseCount('tickets', 0);
        $this->assertNotNull($sourceSystem->fresh()->last_used_at);
    }

    public function test_invalid_or_inactive_lookup_codes_are_rejected(): void
    {
        [, $apiKey] = $this->createSourceSystem();
        TicketPriority::factory()->create(['code' => 'disabled', 'is_active' => false]);
        TicketCategory::factory()->create(['code' => 'disabled', 'is_active' => false]);

        $response = $this->withToken($apiKey)
            ->postJson(route('api.v1.tickets.store'), [
                'external_user_id' => 'customer-10',
                'subject' => 'مشکل در پرداخت',
                'priority_code' => 'disabled',
                'category_code' => 'disabled',
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['priority_code', 'category_code'])
            ->assertJsonPath('errors.priority_code.0', 'اولویت انتخاب‌شده معتبر یا فعال نیست.')
            ->assertJsonPath('errors.category_code.0', 'دسته‌بندی انتخاب‌شده معتبر یا فعال نیست.');
        $this->assertDatabaseCount('tickets', 0);
    }

    public function test_authenticated_system_can_create_ticket_with_standard_json_response(): void
    {
        $this->travelTo('2026-10-13 14:30:00');
        [$sourceSystem, $apiKey] = $this->createSourceSystem();
        $status = TicketStatus::factory()->create([
            'name' => 'جدید',
            'code' => 'new',
            'is_active' => true,
        ]);
        $priority = TicketPriority::factory()->create([
            'name' => 'فوری',
            'code' => 'urgent',
            'is_active' => true,
        ]);
        $category = TicketCategory::factory()->create([
            'name' => 'پرداخت',
            'code' => 'payment',
            'is_active' => true,
        ]);
        $otherSystem = System::factory()->create();

        $response = $this->withToken($apiKey)
            ->postJson(route('api.v1.tickets.store'), [
                'external_user_id' => '  customer-245  ',
                'external_ticket_id' => '  EXT-900  ',
                'subject' => '  خطا در پرداخت سفارش  ',
                'description' => '  پرداخت موفق است اما سفارش ثبت نشده.  ',
                'priority_code' => ' URGENT ',
                'category_code' => ' PAYMENT ',
                'source_system_id' => $otherSystem->getKey(),
                'status_id' => TicketStatus::factory()->create()->getKey(),
                'ticket_number' => 'INJECTED-NUMBER',
                'assigned_to' => $sourceSystem->created_by,
            ]);

        $ticket = Ticket::query()->sole();
        $activity = $ticket->activities()->where('event', 'created')->sole();

        $response
            ->assertCreated()
            ->assertExactJson([
                'data' => [
                    'id' => $ticket->getKey(),
                    'ticket_number' => 'TKT-20261310-0001',
                    'external_user_id' => 'customer-245',
                    'external_ticket_id' => 'EXT-900',
                    'subject' => 'خطا در پرداخت سفارش',
                    'description' => 'پرداخت موفق است اما سفارش ثبت نشده.',
                    'status' => ['code' => 'new', 'name' => 'جدید'],
                    'priority' => ['code' => 'urgent', 'name' => 'فوری'],
                    'category' => ['code' => 'payment', 'name' => 'پرداخت'],
                    'source_system' => [
                        'code' => $sourceSystem->code,
                        'name' => $sourceSystem->name,
                    ],
                    'created_at' => '2026-10-13T14:30:00.000000Z',
                    'updated_at' => '2026-10-13T14:30:00.000000Z',
                ],
                'message' => 'تیکت با موفقیت ثبت شد.',
            ]);

        $this->assertTrue($ticket->sourceSystem->is($sourceSystem));
        $this->assertTrue($ticket->status->is($status));
        $this->assertTrue($ticket->priority->is($priority));
        $this->assertTrue($ticket->category->is($category));
        $this->assertNull($ticket->assigned_to);
        $this->assertSame('TKT-20261310-0001', $ticket->ticket_number);
        $this->assertSame($sourceSystem->getKey(), $activity->attribute_changes->get('attributes')['source_system_id']);
        $this->assertNotNull($sourceSystem->fresh()->last_used_at);
    }

    public function test_default_priority_and_nullable_category_are_applied(): void
    {
        [$sourceSystem, $apiKey] = $this->createSourceSystem();
        TicketStatus::factory()->create(['code' => 'new', 'is_active' => true]);
        $normalPriority = TicketPriority::factory()->create([
            'code' => 'normal',
            'is_active' => true,
        ]);

        $response = $this->withHeader('X-API-Key', $apiKey)
            ->postJson(route('api.v1.tickets.store'), [
                'external_user_id' => 'customer-20',
                'subject' => 'درخواست راهنمایی',
            ]);

        $ticket = Ticket::query()->sole();

        $response
            ->assertCreated()
            ->assertJsonPath('data.priority.code', 'normal')
            ->assertJsonPath('data.category', null);
        $this->assertTrue($ticket->sourceSystem->is($sourceSystem));
        $this->assertTrue($ticket->priority->is($normalPriority));
        $this->assertNull($ticket->category_id);
    }

    /** @return array{System, string} */
    private function createSourceSystem(): array
    {
        $apiKey = 'tkt_'.str_repeat('a', 56);
        $sourceSystem = System::factory()->create();
        $sourceSystem->issueApiKey($apiKey)->saveQuietly();

        return [$sourceSystem, $apiKey];
    }
}
