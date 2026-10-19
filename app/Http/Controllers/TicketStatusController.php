<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTicketStatusRequest;
use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Services\TicketWorkflowService;
use Illuminate\Http\RedirectResponse;

class TicketStatusController extends Controller
{
    public function __invoke(UpdateTicketStatusRequest $request, Ticket $ticket, TicketWorkflowService $workflow): RedirectResponse
    {
        $status = TicketStatus::query()->where('is_active', true)->findOrFail($request->validated('status_id'));
        $workflow->changeStatus($ticket, $status, $request->user(), $request->validated('reason'));

        return back()->with('success', 'وضعیت تیکت با موفقیت تغییر کرد.');
    }
}
