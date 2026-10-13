<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Models\TicketStatusHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TicketStatusHistory>
 */
class TicketStatusHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'from_status_id' => null,
            'to_status_id' => TicketStatus::factory(),
            'changed_by' => null,
            'reason' => fake()->optional()->sentence(),
        ];
    }

    public function fromStatus(TicketStatus $status): static
    {
        return $this->state(fn (array $attributes): array => [
            'from_status_id' => $status->id,
        ]);
    }

    public function changedBy(User $user): static
    {
        return $this->state(fn (array $attributes): array => [
            'changed_by' => $user->id,
        ]);
    }
}
