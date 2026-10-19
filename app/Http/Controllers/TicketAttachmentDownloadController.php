<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketAttachment;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TicketAttachmentDownloadController extends Controller
{
    public function __invoke(Ticket $ticket, TicketAttachment $attachment): StreamedResponse
    {
        Gate::authorize('downloadAttachment', $ticket);
        abort_unless($attachment->ticket_id === $ticket->getKey(), 404);
        abort_unless(config("filesystems.disks.{$attachment->storage_disk}") !== null, 404);

        $disk = Storage::disk($attachment->storage_disk);
        abort_unless($disk->exists($attachment->storage_path), 404);

        return $disk->download($attachment->storage_path, $attachment->original_filename);
    }
}
