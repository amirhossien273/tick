<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketMessageResource extends JsonResource
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
            'sender' => [
                'type' => $this->sender_type,
                'name' => $this->relationLoaded('user') ? $this->user?->name : null,
                'external_user_id' => $this->sender_type === 'customer' ? $this->external_user_id : null,
            ],
            'message' => $this->message,
            'is_edited' => $this->is_edited,
            'edited_at' => $this->edited_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
