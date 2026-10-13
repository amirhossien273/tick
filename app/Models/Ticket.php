<?php

namespace App\Models;

use App\Services\TicketService;
use Database\Factories\TicketFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\HasActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable([
    'source_system_id',
    'external_user_id',
    'external_ticket_id',
    'subject',
    'description',
    'status_id',
    'priority_id',
    'category_id',
    'assigned_to',
    'resolved_at',
    'closed_at',
])]
class Ticket extends Model
{
    /** @use HasFactory<TicketFactory> */
    use HasActivity, HasFactory, HasUuids;

    /** @return BelongsTo<System, $this> */
    public function sourceSystem(): BelongsTo
    {
        return $this->belongsTo(System::class, 'source_system_id');
    }

    /** @return BelongsTo<TicketStatus, $this> */
    public function status(): BelongsTo
    {
        return $this->belongsTo(TicketStatus::class, 'status_id');
    }

    /** @return BelongsTo<TicketPriority, $this> */
    public function priority(): BelongsTo
    {
        return $this->belongsTo(TicketPriority::class, 'priority_id');
    }

    /** @return BelongsTo<TicketCategory, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    /** @return BelongsTo<User, $this> */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /** @return HasMany<TicketMessage, $this> */
    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class);
    }

    /** @return HasMany<TicketAttachment, $this> */
    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    /** @return HasMany<TicketAssignment, $this> */
    public function assignments(): HasMany
    {
        return $this->hasMany(TicketAssignment::class);
    }

    /** @return HasMany<TicketStatusHistory, $this> */
    public function statusHistories(): HasMany
    {
        return $this->hasMany(TicketStatusHistory::class);
    }

    /** @return HasMany<TicketWatcher, $this> */
    public function watchers(): HasMany
    {
        return $this->hasMany(TicketWatcher::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('tickets')
            ->logOnly([
                'ticket_number',
                'source_system_id',
                'external_user_id',
                'external_ticket_id',
                'subject',
                'description',
                'status_id',
                'priority_id',
                'category_id',
                'assigned_to',
                'resolved_at',
                'closed_at',
            ])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected static function booted(): void
    {
        static::creating(function (Ticket $ticket): void {
            $ticket->ticket_number = TicketService::generateUniqueNumber();
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }
}
