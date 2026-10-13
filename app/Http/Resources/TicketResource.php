<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getKey(),
            'ticket_number' => $this->ticket_number,
            'external_user_id' => $this->external_user_id,
            'external_ticket_id' => $this->external_ticket_id,
            'subject' => $this->subject,
            'description' => $this->description,
            'status' => $this->whenLoaded('status', fn (): array => [
                'code' => $this->status->code,
                'name' => $this->status->name,
            ]),
            'priority' => $this->whenLoaded('priority', fn (): array => [
                'code' => $this->priority->code,
                'name' => $this->priority->name,
            ]),
            'category' => $this->when(
                $this->relationLoaded('category'),
                fn (): ?array => $this->category === null ? null : [
                    'code' => $this->category->code,
                    'name' => $this->category->name,
                ],
            ),
            'source_system' => $this->whenLoaded('sourceSystem', fn (): array => [
                'code' => $this->sourceSystem->code,
                'name' => $this->sourceSystem->name,
            ]),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
