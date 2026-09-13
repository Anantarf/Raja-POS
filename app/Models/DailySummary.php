<?php

namespace App\Models;

use App\Traits\ScopeLocation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailySummary extends Model
{
    use HasFactory, ScopeLocation;

    protected $fillable = [
        'location_id',
        'summary_date',
        'status', // 'BELUM_DICEK', 'SEDANG_DICEK', 'SUDAH_DICEK'
        'has_discrepancy',
        'dana_saldo_awal',
        'dana_topup',
        'dana_trx',
        'dana_saldo_android',
        'qris_tarik_tunai',
        'qris_saldo_android',
        'bankmas_saldo_awal',
        'bankmas_topup',
        'bankmas_trx',
        'bankmas_saldo_android',
        'multi_saldo_awal',
        'multi_topup',
        'multi_trx',
        'multi_saldo_android',
        'tarik_tunai_kasir',
        'notes',
        'created_by',
        'verified_at',
        'verified_by',
    ];

    protected $casts = [
        'summary_date' => 'date:Y-m-d',
        'has_discrepancy' => 'boolean',
        'verified_at' => 'datetime',
        'dana_saldo_awal' => 'decimal:2',
        'dana_topup' => 'decimal:2',
        'dana_trx' => 'decimal:2',
        'dana_saldo_android' => 'decimal:2',
        'qris_tarik_tunai' => 'decimal:2',
        'qris_saldo_android' => 'decimal:2',
        'bankmas_saldo_awal' => 'decimal:2',
        'bankmas_topup' => 'decimal:2',
        'bankmas_trx' => 'decimal:2',
        'bankmas_saldo_android' => 'decimal:2',
        'multi_saldo_awal' => 'decimal:2',
        'multi_topup' => 'decimal:2',
        'multi_trx' => 'decimal:2',
        'multi_saldo_android' => 'decimal:2',
        'tarik_tunai_kasir' => 'decimal:2',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
