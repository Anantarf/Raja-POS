<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class BalanceTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_number',
        'transaction_type',
        'source_account_id',
        'destination_account_id',
        'amount',
        'balance_before',
        'balance_after',
        'destination_balance_before',
        'destination_balance_after',
        'reference_type',
        'reference_id',
        'description',
        'created_by',
        'transaction_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'destination_balance_before' => 'decimal:2',
        'destination_balance_after' => 'decimal:2',
        'transaction_date' => 'datetime',
    ];

    public function sourceAccount(): BelongsTo
    {
        return $this->belongsTo(BalanceAccount::class, 'source_account_id');
    }

    public function destinationAccount(): BelongsTo
    {
        return $this->belongsTo(BalanceAccount::class, 'destination_account_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeForUserLocation($query, ?User $user = null)
    {
        $user = $user ?? auth()->user();

        if (! $user || $user->hasGlobalLocationAccess()) {
            return $query;
        }

        if (! $user->location_id) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function ($q) use ($user) {
            $q->whereHas('sourceAccount', function ($sq) use ($user) {
                $sq->forUserLocation($user);
            })->orWhereHas('destinationAccount', function ($sq) use ($user) {
                $sq->forUserLocation($user);
            });
        });
    }

    /**
     * Generate concise, unique transaction number for balance mutations.
     * Example: TRX-260905-A8B9C or TRF-260905-K3N8P
     */
    public static function generateTransactionNumber(string $prefix = 'TRX'): string
    {
        $cleanPrefix = strtoupper(trim($prefix)) ?: 'TRX';
        $dateStr = date('ymd');

        do {
            $number = $cleanPrefix . '-' . $dateStr . '-' . strtoupper(Str::random(5));
        } while (static::where('transaction_number', $number)->exists());

        return $number;
    }

    /**
     * Format legacy or existing long transaction numbers cleanly.
     */
    public function getTransactionNumberAttribute($value): string
    {
        $number = trim((string) ($value ?? ''));

        if ($number === '') {
            return '-';
        }

        if (strlen($number) > 20 && str_contains($number, '-')) {
            $parts = array_values(array_filter(explode('-', $number)));
            $prefix = strtoupper(!empty($parts[0]) ? $parts[0] : 'TRX');
            $code = strtoupper(!empty($parts[1]) ? $parts[1] : substr($number, -8));
            return $prefix . '-' . substr($code, 0, 8);
        }

        return strtoupper($number);
    }

    public function getFormattedTransactionNumberAttribute(): string
    {
        return $this->transaction_number;
    }

    public function getDescriptionAttribute($value): string
    {
        if (empty($value)) {
            return '';
        }

        return preg_replace_callback('/(TRX)-([a-f0-9]{8})-[a-f0-9-]{20,}/i', function ($m) {
            return strtoupper($m[1] . '-' . substr($m[2], 0, 8));
        }, (string) $value);
    }
}
