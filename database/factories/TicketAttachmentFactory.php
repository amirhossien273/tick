<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TicketAttachment>
 */
class TicketAttachmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $extension = fake()->randomElement(['jpg', 'pdf', 'png', 'txt']);

        return [
            'ticket_id' => Ticket::factory(),
            'ticket_message_id' => null,
            'original_filename' => fake()->word().'.'.$extension,
            'storage_disk' => 'local',
            'storage_path' => 'ticket-attachments/'.fake()->uuid().'.'.$extension,
            'mime_type' => fake()->randomElement(['image/jpeg', 'application/pdf', 'image/png', 'text/plain']),
            'file_extension' => $extension,
            'file_size' => fake()->numberBetween(1, 10_000_000),
            'uploaded_by_user_id' => null,
            'external_user_id' => fake()->uuid(),
        ];
    }

    public function forMessage(TicketMessage $message): static
    {
        return $this->state(fn (array $attributes): array => [
            'ticket_id' => $message->ticket_id,
            'ticket_message_id' => $message->id,
        ]);
    }

    public function uploadedBy(User $user): static
    {
        return $this->state(fn (array $attributes): array => [
            'uploaded_by_user_id' => $user->id,
            'external_user_id' => null,
        ]);
    }
}
