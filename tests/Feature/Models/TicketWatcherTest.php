<?php

namespace Tests\Feature\Models;

use App\Models\Ticket;
use App\Models\TicketWatcher;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class TicketWatcherTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_created_watcher_exposes_relationships_and_records_activity(): void
    {
        $ticket = Ticket::factory()->create();
        $user = User::factory()->create();

        $watcher = TicketWatcher::factory()->create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
        ])->refresh();

        $activity = $watcher->activities()->where('event', 'created')->sole();
        $attributes = $activity->attribute_changes->get('attributes');

        $this->assertModelExists($watcher);
        $this->assertTrue(Str::isUuid($watcher->id));
        $this->assertTrue($watcher->ticket->is($ticket));
        $this->assertTrue($watcher->user->is($user));
        $this->assertTrue($ticket->watchers->contains($watcher));

        $this->assertSame('ticket_watchers', $activity->log_name);
        $this->assertTrue($activity->subject->is($watcher));
        $this->assertSame($ticket->id, $attributes['ticket_id']);
        $this->assertSame($user->id, $attributes['user_id']);
    }

    public function test_same_user_cannot_watch_same_ticket_more_than_once(): void
    {
        $ticket = Ticket::factory()->create();
        $user = User::factory()->create();
        TicketWatcher::factory()->create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
        ]);

        $this->expectException(QueryException::class);

        TicketWatcher::factory()->create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_deleting_ticket_removes_its_watchers(): void
    {
        $ticket = Ticket::factory()->create();
        $watcher = TicketWatcher::factory()->create([
            'ticket_id' => $ticket->id,
        ]);

        $ticket->delete();

        $this->assertModelMissing($watcher);
    }

    public function test_deleting_user_removes_their_watcher_records(): void
    {
        $user = User::factory()->create();
        $watcher = TicketWatcher::factory()->create([
            'user_id' => $user->id,
        ]);

        $user->delete();

        $this->assertModelMissing($watcher);
    }
}
