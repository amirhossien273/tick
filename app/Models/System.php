<?php

namespace App\Models;

use Database\Factories\SystemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Concerns\HasActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable(['name', 'description', 'api_key', 'api_key_hash', 'is_active', 'last_used_at', 'created_by'])]
#[Hidden(['api_key', 'api_key_hash'])]
class System extends Model
{
    /** @use HasFactory<SystemFactory> */
    use HasActivity, HasFactory, HasUuids;

    /** @return BelongsTo<User, $this> */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('systems')
            ->logOnly(['name', 'code', 'description', 'is_active', 'last_used_at', 'created_by'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function issueApiKey(string $apiKey): static
    {
        $this->forceFill([
            'api_key' => $apiKey,
            'api_key_hash' => self::hashApiKey($apiKey),
        ]);

        return $this;
    }

    public static function hashApiKey(string $apiKey): string
    {
        return hash('sha256', $apiKey);
    }

    protected static function booted(): void
    {
        static::creating(function (System $system): void {
            $issuedAt = now();
            $sequence = self::nextSequenceForYear((int) $issuedAt->format('Y'));

            $system->code = sprintf(
                'SYS-%s-%04d',
                $issuedAt->format('Ydm'),
                $sequence,
            );
        });
    }

    private static function nextSequenceForYear(int $year): int
    {
        return DB::transaction(function () use ($year): int {
            DB::table('system_code_sequences')->insertOrIgnore([
                'year' => $year,
                'last_number' => 0,
            ]);

            $currentSequence = DB::table('system_code_sequences')
                ->where('year', $year)
                ->lockForUpdate()
                ->value('last_number');

            $nextSequence = (int) $currentSequence + 1;

            DB::table('system_code_sequences')
                ->where('year', $year)
                ->update(['last_number' => $nextSequence]);

            return $nextSequence;
        }, 5);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'api_key' => 'encrypted',
            'is_active' => 'boolean',
            'last_used_at' => 'datetime',
        ];
    }
}
