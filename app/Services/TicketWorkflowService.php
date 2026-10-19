<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketMessage;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\TicketStatusHistory;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class TicketWorkflowService
{
    /** @param array<int, UploadedFile> $attachments */
    public function addMessage(Ticket $ticket, User $author, string $message, bool $isInternalNote, array $attachments = []): TicketMessage
    {
        $storedPaths = [];

        try {
            return DB::transaction(function () use ($ticket, $author, $message, $isInternalNote, $attachments, &$storedPaths): TicketMessage {
                $ticketMessage = $ticket->messages()->create([
                    'sender_type' => 'agent', 'user_id' => $author->getKey(), 'external_user_id' => null,
                    'message' => $message, 'is_internal_note' => $isInternalNote, 'is_edited' => false, 'edited_at' => null,
                ]);

                foreach ($attachments as $attachment) {
                    $path = $attachment->store("ticket-attachments/{$ticket->getKey()}", 'local');
                    $storedPaths[] = $path;
                    TicketAttachment::query()->create([
                        'ticket_id' => $ticket->getKey(), 'ticket_message_id' => $ticketMessage->getKey(),
                        'original_filename' => $attachment->getClientOriginalName(), 'storage_disk' => 'local',
                        'storage_path' => $path, 'mime_type' => $attachment->getMimeType(),
                        'file_extension' => mb_strtolower($attachment->getClientOriginalExtension()) ?: null,
                        'file_size' => $attachment->getSize(), 'uploaded_by_user_id' => $author->getKey(), 'external_user_id' => null,
                    ]);
                }

                $ticket->touch();

                return $ticketMessage->load(['user:id,name', 'attachments.uploader:id,name']);
            });
        } catch (Throwable $exception) {
            if ($storedPaths !== []) {
                Storage::disk('local')->delete($storedPaths);
            }

            throw $exception;
        }
    }

    public function changeStatus(Ticket $ticket, TicketStatus $status, User $actor, ?string $reason): void
    {
        DB::transaction(function () use ($ticket, $status, $actor, $reason): void {
            /** @var Ticket $lockedTicket */
            $lockedTicket = Ticket::query()->lockForUpdate()->findOrFail($ticket->getKey());
            $statusCode = mb_strtolower($status->code);

            TicketStatusHistory::query()->create([
                'ticket_id' => $lockedTicket->getKey(), 'from_status_id' => $lockedTicket->status_id,
                'to_status_id' => $status->getKey(), 'changed_by' => $actor->getKey(), 'reason' => $reason,
            ]);

            $lockedTicket->status_id = $status->getKey();
            $lockedTicket->resolved_at = in_array($statusCode, ['resolved', 'closed'], true)
                ? ($lockedTicket->resolved_at ?? now())
                : null;
            $lockedTicket->closed_at = $statusCode === 'closed' ? ($lockedTicket->closed_at ?? now()) : null;
            $lockedTicket->save();
        });
    }

    public function changePriority(Ticket $ticket, TicketPriority $priority): void
    {
        DB::transaction(fn () => Ticket::query()->lockForUpdate()->findOrFail($ticket->getKey())->update([
            'priority_id' => $priority->getKey(),
        ]));
    }

    public function assign(Ticket $ticket, ?User $assignee, User $assigner, ?string $reason): void
    {
        DB::transaction(function () use ($ticket, $assignee, $assigner, $reason): void {
            /** @var Ticket $lockedTicket */
            $lockedTicket = Ticket::query()->lockForUpdate()->findOrFail($ticket->getKey());
            $lockedTicket->assignments()->whereNull('unassigned_at')->update(['unassigned_at' => now()]);
            $lockedTicket->update(['assigned_to' => $assignee?->getKey()]);

            if ($assignee !== null) {
                $lockedTicket->assignments()->create([
                    'assigned_to' => $assignee->getKey(), 'assigned_by' => $assigner->getKey(),
                    'assigned_at' => now(), 'unassigned_at' => null, 'reason' => $reason,
                ]);
            }
        });
    }
}
