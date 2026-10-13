<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\TicketAssignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TicketAssignment>
 */
class TicketAssignmentFactory extends Factory
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
            'assigned_to' => User::factory(),
            'assigned_by' => null,
            'assigned_at' => now(),
            'unassigned_at' => null,
            'reason' => fake()->optional()->sentence(),
        ];
    }

    public function assignedBy(User $user): static
    {
        return $this->state(fn (array $attributes): array => [
            'assigned_by' => $user->id,
        ]);
    }

    public function ended(): static
    {
        return $this->state(fn (array $attributes): array => [
            'unassigned_at' => now(),
        ]);
    }
}
