<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'barcode',
        'image_path',
        'name',
        'category_id',
        'brand_id',
        'product_type',
        'product_subtype',
        'default_balance_account_id',
        'cost_price',
        'selling_price',
        'discount_type',
        'discount_value',
        'is_discount_active',
        'minimum_stock',
        'status',
        'price_status',
        'description',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'is_discount_active' => 'boolean',
        'minimum_stock' => 'integer',
    ];

    public function getUnitDiscountAmountAttribute(): float
    {
        if (! $this->is_discount_active || $this->discount_type === 'NONE' || (float) $this->discount_value <= 0) {
            return 0.0;
        }

        $selling = (float) $this->selling_price;

        if ($this->discount_type === 'PERCENTAGE') {
            return round($selling * ((float) $this->discount_value / 100), 2);
        }

        return min($selling, (float) $this->discount_value);
    }

    public function getEffectiveSellingPriceAttribute(): float
    {
        $selling = (float) $this->selling_price;
        $discount = $this->unit_discount_amount;

        return max(0.0, round($selling - $discount, 2));
    }

    public function getHasActiveDiscountAttribute(): bool
    {
        return $this->is_discount_active && $this->discount_type !== 'NONE' && $this->unit_discount_amount > 0;
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($product) {
            if (filled($product->name)) {
                $product->name = Str::upper(trim($product->name));
            }

            // Price status logic (PRD 7.1)
            if ($product->product_type !== 'LAYANAN' && ((float) $product->cost_price <= 0 || (float) $product->selling_price <= 0)) {
                $product->price_status = 'INCOMPLETE';
            } else {
                $product->price_status = 'COMPLETE';
            }
        });
    }

    public function getEffectiveBarcodeAttribute(): string
    {
        return ! empty($this->barcode) ? $this->barcode : $this->code;
    }

    public function getImageUrlAttribute(): string
    {
        if (! empty($this->image_path) && Storage::disk('public')->exists($this->image_path)) {
            return Storage::url($this->image_path);
        }

        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&color=1E3A8A&background=EEF4FF';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function defaultBalanceAccount(): BelongsTo
    {
        return $this->belongsTo(BalanceAccount::class, 'default_balance_account_id');
    }
}
