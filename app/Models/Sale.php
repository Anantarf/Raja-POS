<?php

namespace App\Models;

use App\Traits\ScopeLocation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, ScopeLocation, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'idempotency_key',
        'cashier_id',
        'location_id',
        'transaction_date',
        'subtotal',
        'discount_amount',
        'total_amount',
        'amount_paid',
        'change_amount',
        'trash_reason',
        'trashed_by',
        'trashed_at',
        'restored_by',
        'restored_at',
        'total_cost',
        'gross_profit',
        'status',
        'notes',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'trashed_at' => 'datetime',
        'restored_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'gross_profit' => 'decimal:2',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getInvoiceNumberAttribute($value): string
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

    public function getGrandTotalAttribute()
    {
        return $this->attributes['total_amount'] ?? 0;
    }

    public function getPaidAmountAttribute()
    {
        return $this->attributes['amount_paid'] ?? 0;
    }
}
