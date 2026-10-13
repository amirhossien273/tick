<?php

namespace App\Models;

use Database\Factories\TicketMessageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\HasActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable([
    'ticket_id',
    'sender_type',
    'user_id',
    'external_user_id',
    'message',
    'is_internal_note',
    'is_edited',
    'edited_at',
])]
class TicketMessage extends Model
{
    /** @use HasFactory<TicketMessageFactory> */
    use HasActivity, HasFactory, HasUuids;

    /** @return BelongsTo<Ticket, $this> */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<TicketAttachment, $this> */
    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('ticket_messages')
            ->logOnly([
                'ticket_id',
                'sender_type',
                'user_id',
                'external_user_id',
                'message',
                'is_internal_note',
                'is_edited',
                'edited_at',
            ])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_internal_note' => 'boolean',
            'is_edited' => 'boolean',
            'edited_at' => 'datetime',
        ];
    }
}
