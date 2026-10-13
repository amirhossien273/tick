<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TicketMessage>
 */
class TicketMessageFactory extends Factory
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
            'sender_type' => 'customer',
            'user_id' => null,
            'external_user_id' => fake()->uuid(),
            'message' => fake()->paragraph(),
            'is_internal_note' => false,
            'is_edited' => false,
            'edited_at' => null,
        ];
    }

    public function fromAgent(User $user): static
    {
        return $this->state(fn (array $attributes): array => [
            'sender_type' => 'agent',
            'user_id' => $user->id,
            'external_user_id' => null,
        ]);
    }

    public function fromSystem(): static
    {
        return $this->state(fn (array $attributes): array => [
            'sender_type' => 'system',
            'user_id' => null,
            'external_user_id' => null,
        ]);
    }

    public function internalNote(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_internal_note' => true,
        ]);
    }
}
