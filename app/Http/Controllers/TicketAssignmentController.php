<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTicketAssignmentRequest;
use App\Models\Ticket;
use App\Models\User;
use App\Services\TicketWorkflowService;
use Illuminate\Http\RedirectResponse;

class TicketAssignmentController extends Controller
{
    public function __invoke(UpdateTicketAssignmentRequest $request, Ticket $ticket, TicketWorkflowService $workflow): RedirectResponse
    {
        $assigneeId = $request->validated('assigned_to');
        $assignee = $assigneeId === null ? null : User::query()->where('is_active', true)->findOrFail($assigneeId);
        $workflow->assign($ticket, $assignee, $request->user(), $request->validated('reason'));

        return back()->with('success', $assignee === null ? 'تخصیص تیکت برداشته شد.' : "تیکت به {$assignee->name} تخصیص یافت.");
    }
}
