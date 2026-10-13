<?php

namespace App\Services;

use App\Models\System;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketMessage;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class TicketService
{
    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Ticket>
     */
    public function paginateForSystem(System $sourceSystem, array $filters): LengthAwarePaginator
    {
        return $this->queryForSystem($sourceSystem)
            ->with([
                'sourceSystem:id,name,code',
                'status:id,name,code',
                'priority:id,name,code',
                'category:id,name,code',
            ])
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query
                        ->where('ticket_number', 'like', "%{$search}%")
                        ->orWhere('external_ticket_id', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%");
                });
            })
            ->when(
                $filters['external_user_id'] ?? null,
                fn (Builder $query, string $externalUserId): Builder => $query->where('external_user_id', $externalUserId),
            )
            ->when(
                $filters['status_code'] ?? null,
                fn (Builder $query, string $statusCode): Builder => $query->whereRelation('status', 'code', $statusCode),
            )
            ->when(
                $filters['priority_code'] ?? null,
                fn (Builder $query, string $priorityCode): Builder => $query->whereRelation('priority', 'code', $priorityCode),
            )
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate((int) ($filters['per_page'] ?? 15))
            ->withQueryString();
    }

    public function findForSystemOrFail(System $sourceSystem, string $ticketId): Ticket
    {
        return $this->queryForSystem($sourceSystem)
            ->with([
                'sourceSystem:id,name,code',
                'status:id,name,code',
                'priority:id,name,code',
                'category:id,name,code',
            ])
            ->findOrFail($ticketId);
    }

    /** @return LengthAwarePaginator<int, TicketMessage> */
    public function paginateMessagesForSystem(
        System $sourceSystem,
        string $ticketId,
        int $perPage = 50,
    ): LengthAwarePaginator {
        $ticket = $this->queryForSystem($sourceSystem)->findOrFail($ticketId);

        return $ticket->messages()
            ->where('is_internal_note', false)
            ->with('user:id,name')
            ->orderBy('created_at')
            ->orderBy('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    /** @param array{message: string} $attributes */
    public function createCustomerMessage(
        System $sourceSystem,
        string $ticketId,
        array $attributes,
    ): TicketMessage {
        return DB::transaction(function () use ($sourceSystem, $ticketId, $attributes): TicketMessage {
            $ticket = $this->queryForSystem($sourceSystem)->findOrFail($ticketId);

            $message = $ticket->messages()->create([
                'sender_type' => 'customer',
                'user_id' => null,
                'external_user_id' => $ticket->external_user_id,
                'message' => $attributes['message'],
                'is_internal_note' => false,
                'is_edited' => false,
                'edited_at' => null,
            ]);

            $ticket->touch();

            return $message->load('user:id,name');
        }, 5);
    }

    /** @param array<string, mixed> $attributes */
    public function create(System $sourceSystem, array $attributes): Ticket
    {
        return DB::transaction(function () use ($sourceSystem, $attributes): Ticket {
            $status = TicketStatus::query()
                ->where('code', 'new')
                ->where('is_active', true)
                ->firstOrFail();

            $priority = TicketPriority::query()
                ->where('code', $attributes['priority_code'] ?? 'normal')
                ->where('is_active', true)
                ->firstOrFail();

            $category = isset($attributes['category_code'])
                ? TicketCategory::query()
                    ->where('code', $attributes['category_code'])
                    ->where('is_active', true)
                    ->firstOrFail()
                : null;

            $ticket = Ticket::query()->create([
                'source_system_id' => $sourceSystem->getKey(),
                'external_user_id' => $attributes['external_user_id'],
                'external_ticket_id' => $attributes['external_ticket_id'] ?? null,
                'subject' => $attributes['subject'],
                'description' => $attributes['description'] ?? null,
                'status_id' => $status->getKey(),
                'priority_id' => $priority->getKey(),
                'category_id' => $category?->getKey(),
            ]);

            return $ticket->load([
                'sourceSystem:id,name,code',
                'status:id,name,code',
                'priority:id,name,code',
                'category:id,name,code',
            ]);
        }, 5);
    }

    public static function generateUniqueNumber(): string
    {
        $issuedAt = now();
        $year = (int) $issuedAt->format('Y');

        $sequence = DB::transaction(function () use ($year): int {
            DB::table('ticket_number_sequences')->insertOrIgnore([
                'year' => $year,
                'last_number' => 0,
            ]);

            $currentSequence = DB::table('ticket_number_sequences')
                ->where('year', $year)
                ->lockForUpdate()
                ->value('last_number');

            $nextSequence = (int) $currentSequence + 1;

            DB::table('ticket_number_sequences')
                ->where('year', $year)
                ->update(['last_number' => $nextSequence]);

            return $nextSequence;
        }, 5);

        return sprintf('TKT-%s-%04d', $issuedAt->format('Ydm'), $sequence);
    }

    /** @return Builder<Ticket> */
    private function queryForSystem(System $sourceSystem): Builder
    {
        return Ticket::query()->whereBelongsTo($sourceSystem, 'sourceSystem');
    }
}
