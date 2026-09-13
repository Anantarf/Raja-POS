<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'receipt_label',
        'code',
        'type',
        'status',
    ];

    public function getReceiptDisplayNameAttribute(): string
    {
        return filled($this->receipt_label) ? $this->receipt_label : $this->name;
    }
}
