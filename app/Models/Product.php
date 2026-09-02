<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

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
        'minimum_stock',
        'status',
        'price_status',
        'description',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'minimum_stock' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($product) {
            // Price status logic (PRD 7.1)
            if ((float) $product->cost_price <= 0 || (float) $product->selling_price <= 0) {
                $product->price_status = 'INCOMPLETE';
            } else {
                $product->price_status = 'COMPLETE';
            }
        });
    }

    public function getEffectiveBarcodeAttribute(): string
    {
        return !empty($this->barcode) ? $this->barcode : $this->code;
    }

    public function getImageUrlAttribute(): string
    {
        if (!empty($this->image_path) && Storage::disk('public')->exists($this->image_path)) {
            return Storage::url($this->image_path);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=1E3A8A&background=EEF4FF';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function defaultBalanceAccount(): BelongsTo
    {
        return $this->belongsTo(BalanceAccount::class, 'default_balance_account_id');
    }
}
