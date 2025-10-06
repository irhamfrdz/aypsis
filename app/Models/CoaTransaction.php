<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoaTransaction extends Model
{
    protected $fillable = [
        'coa_id',
        'tanggal_transaksi',
        'nomor_referensi',
        'jenis_transaksi',
        'keterangan',
        'debit',
        'kredit',
        'saldo',
        'created_by'
    ];

    protected $casts = [
        'tanggal_transaksi' => 'date',
        'debit' => 'decimal:2',
        'kredit' => 'decimal:2',
        'saldo' => 'decimal:2'
    ];

    /**
     * Relasi ke akun COA
     */
    public function coa(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'coa_id');
    }

    /**
     * Relasi ke user yang membuat transaksi
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
