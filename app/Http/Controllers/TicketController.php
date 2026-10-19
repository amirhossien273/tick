<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexTicketManagementRequest;
use App\Models\System;
use App\Models\Ticket;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(IndexTicketManagementRequest $request): View
    {
        $filters = $request->safe()->only(['search', 'source_system_id', 'status_id', 'priority_id', 'assigned_to', 'per_page']);
        $tickets = Ticket::query()
            ->with(['sourceSystem:id,name,code', 'status:id,name,code', 'priority:id,name,code', 'assignee:id,name'])
            ->withCount(['messages', 'attachments'])
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('ticket_number', 'like', "%{$search}%")->orWhere('subject', 'like', "%{$search}%")
                        ->orWhere('external_ticket_id', 'like', "%{$search}%")->orWhere('external_user_id', 'like', "%{$search}%");
                });
            })
            ->when($filters['source_system_id'] ?? null, fn (Builder $query, string $id): Builder => $query->where('source_system_id', $id))
            ->when($filters['status_id'] ?? null, fn (Builder $query, string $id): Builder => $query->where('status_id', $id))
            ->when($filters['priority_id'] ?? null, fn (Builder $query, string $id): Builder => $query->where('priority_id', $id))
            ->when(($filters['assigned_to'] ?? null) === 'unassigned', fn (Builder $query): Builder => $query->whereNull('assigned_to'))
            ->when(($filters['assigned_to'] ?? null) && $filters['assigned_to'] !== 'unassigned', fn (Builder $query): Builder => $query->where('assigned_to', $filters['assigned_to']))
            ->latest('updated_at')->latest('id')->paginate((int) ($filters['per_page'] ?? 20))->withQueryString();

        $closedStatusIds = TicketStatus::query()->whereIn('code', ['closed', 'cancelled'])->pluck('id');

        return view('tickets.index', [
            'tickets' => $tickets, 'filters' => $filters,
            'statistics' => [
                'total' => Ticket::query()->count(),
                'active' => Ticket::query()->when($closedStatusIds->isNotEmpty(), fn (Builder $query): Builder => $query->whereNotIn('status_id', $closedStatusIds))->count(),
                'unassigned' => Ticket::query()->whereNull('assigned_to')->count(),
                'mine' => Ticket::query()->where('assigned_to', $request->user()->getKey())->count(),
            ],
            'systems' => System::query()->orderBy('name')->get(['id', 'name']),
            'statuses' => TicketStatus::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(['id', 'name', 'code']),
            'priorities' => TicketPriority::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(['id', 'name', 'code']),
            'agents' => User::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function show(Ticket $ticket): View
    {
        Gate::authorize('view', $ticket);
        $ticket->load([
            'sourceSystem:id,name,code', 'status:id,name,code', 'priority:id,name,code', 'category:id,name,code', 'assignee:id,name,email',
            'messages' => fn ($query) => $query->oldest()->with(['user:id,name', 'attachments.uploader:id,name']),
            'attachments.uploader:id,name',
            'assignments' => fn ($query) => $query->latest('assigned_at')->with(['assignee:id,name', 'assigner:id,name']),
            'statusHistories' => fn ($query) => $query->latest('created_at')->with(['fromStatus:id,name,code', 'toStatus:id,name,code', 'changer:id,name']),
            'activities' => fn ($query) => $query->with('causer:id,name')->latest()->limit(20),
        ]);

        return view('tickets.show', [
            'ticket' => $ticket,
            'statuses' => TicketStatus::query()->where('is_active', true)->orderBy('sort_order')->get(['id', 'name', 'code']),
            'priorities' => TicketPriority::query()->where('is_active', true)->orderBy('sort_order')->get(['id', 'name', 'code']),
            'agents' => User::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'email']),
        ]);
    }
}
