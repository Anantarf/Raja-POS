<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BalanceAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'account_type',
        'current_balance',
        'status',
    ];

    protected $casts = [
        'current_balance' => 'decimal:2',
    ];

    public function getBalanceAttribute()
    {
        return $this->current_balance;
    }
}
