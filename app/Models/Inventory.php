<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'location_id',
        'quantity',
        'reserved_quantity',
        'last_stock_at',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'reserved_quantity' => 'integer',
        'last_stock_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function getStockStatusAttribute(): string
    {
        $minStock = $this->product->minimum_stock ?? 3;

        if ($this->quantity <= 0) {
            return 'OUT_OF_STOCK'; // HABIS
        }

        if ($this->quantity <= $minStock) {
            return 'LOW_STOCK'; // MENIPIS
        }

        return 'AVAILABLE'; // TERSEDIA
    }

    public function getStockStatusLabelAttribute(): string
    {
        return match ($this->stock_status) {
            'OUT_OF_STOCK' => 'HABIS',
            'LOW_STOCK' => 'MENIPIS',
            'AVAILABLE' => 'TERSEDIA',
            default => $this->stock_status,
        };
    }
}
