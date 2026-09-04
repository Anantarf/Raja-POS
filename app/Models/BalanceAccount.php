<?php

namespace App\Models;

use App\Traits\ScopeLocation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BalanceAccount extends Model
{
    use HasFactory, ScopeLocation;

    protected $fillable = [
        'name',
        'code',
        'account_type',
        'location_id',
        'current_balance',
        'status',
    ];

    protected $casts = [
        'current_balance' => 'decimal:2',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function getBalanceAttribute()
    {
        return $this->current_balance;
    }
}
