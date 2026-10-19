<?php

namespace Tests\Unit\Policies;

use App\Models\Ticket;
use App\Models\User;
use App\Policies\TicketPolicy;
use PHPUnit\Framework\TestCase;

class TicketPolicyTest extends TestCase
{
    public function test_active_users_can_view_update_and_download_ticket_attachments(): void
    {
        $user = new User(['is_active' => true]);
        $ticket = new Ticket;
        $policy = new TicketPolicy;

        $this->assertTrue($policy->viewAny($user));
        $this->assertTrue($policy->view($user, $ticket));
        $this->assertTrue($policy->update($user, $ticket));
        $this->assertTrue($policy->downloadAttachment($user, $ticket));
        $this->assertFalse($policy->create($user));
        $this->assertFalse($policy->delete($user, $ticket));
    }

    public function test_inactive_users_cannot_access_tickets(): void
    {
        $user = new User(['is_active' => false]);
        $ticket = new Ticket;
        $policy = new TicketPolicy;

        $this->assertFalse($policy->viewAny($user));
        $this->assertFalse($policy->view($user, $ticket));
        $this->assertFalse($policy->update($user, $ticket));
        $this->assertFalse($policy->downloadAttachment($user, $ticket));
    }
}
