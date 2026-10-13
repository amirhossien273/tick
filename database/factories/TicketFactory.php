<?php

namespace Database\Factories;

use App\Models\System;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'source_system_id' => System::factory(),
            'external_user_id' => fake()->uuid(),
            'external_ticket_id' => fake()->optional()->uuid(),
            'subject' => fake()->sentence(),
            'description' => fake()->optional()->paragraph(),
            'status_id' => TicketStatus::factory(),
            'priority_id' => TicketPriority::factory(),
            'category_id' => TicketCategory::factory(),
            'assigned_to' => null,
            'resolved_at' => null,
            'closed_at' => null,
        ];
    }
}
