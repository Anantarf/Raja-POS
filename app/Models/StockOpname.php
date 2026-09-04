<?php

namespace App\Models;

use App\Traits\ScopeLocation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockOpname extends Model
{
    use HasFactory, ScopeLocation;

    protected $fillable = [
        'opname_number',
        'location_id',
        'status',
        'started_at',
        'completed_at',
        'created_by',
        'approved_by',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockOpnameItem::class);
    }

    /**
     * Generate concise, unique reference number for stock opname sessions.
     * Example: OPN-260905-A1B2C or OPN-BULK-260905-D3E4F
     */
    public static function generateOpnameNumber(string $prefix = 'OPN'): string
    {
        $cleanPrefix = strtoupper(trim($prefix)) ?: 'OPN';
        $dateStr = date('ymd');

        do {
            $number = $cleanPrefix . '-' . $dateStr . '-' . strtoupper(\Illuminate\Support\Str::random(5));
        } while (static::where('opname_number', $number)->exists());

        return $number;
    }

    /**
     * Format legacy or existing long opname numbers cleanly.
     */
    public function getFormattedOpnameNumberAttribute(): string
    {
        $number = trim((string) ($this->opname_number ?? ''));

        if ($number === '') {
            return '-';
        }

        if (strlen($number) > 20 && str_contains($number, '-')) {
            $parts = array_values(array_filter(explode('-', $number)));
            $prefix = strtoupper(!empty($parts[0]) ? $parts[0] : 'OPN');
            $code = strtoupper(end($parts));
            return $prefix . '-' . substr($code, 0, 8);
        }

        return strtoupper($number);
    }
}
