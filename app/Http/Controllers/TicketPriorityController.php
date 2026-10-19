<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTicketPriorityRequest;
use App\Models\Ticket;
use App\Models\TicketPriority;
use App\Services\TicketWorkflowService;
use Illuminate\Http\RedirectResponse;

class TicketPriorityController extends Controller
{
    public function __invoke(UpdateTicketPriorityRequest $request, Ticket $ticket, TicketWorkflowService $workflow): RedirectResponse
    {
        $priority = TicketPriority::query()->where('is_active', true)->findOrFail($request->validated('priority_id'));
        $workflow->changePriority($ticket, $priority);

        return back()->with('success', 'اولویت تیکت با موفقیت تغییر کرد.');
    }
}
