<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAgentTicketMessageRequest;
use App\Models\Ticket;
use App\Services\TicketWorkflowService;
use Illuminate\Http\RedirectResponse;

class TicketMessageController extends Controller
{
    public function __invoke(StoreAgentTicketMessageRequest $request, Ticket $ticket, TicketWorkflowService $workflow): RedirectResponse
    {
        $validated = $request->validated();
        $workflow->addMessage($ticket, $request->user(), $validated['message'], (bool) $validated['is_internal_note'], $request->file('attachments', []));

        return redirect()->route('tickets.show', $ticket)->with(
            'success',
            $validated['is_internal_note'] ? 'یادداشت داخلی ثبت شد.' : 'پاسخ با موفقیت برای مشتری ارسال شد.',
        );
    }
}
