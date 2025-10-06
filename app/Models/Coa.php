<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coa extends Model
{
    protected $table = 'akun_coa';

    protected $fillable = [
        'nomor_akun',
        'kode_nomor',
        'nama_akun',
        'tipe_akun',
        'saldo'
    ];

    protected $casts = [
        'saldo' => 'decimal:2'
    ];

    /**
     * Relasi ke transaksi COA
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(CoaTransaction::class, 'coa_id');
    }
}
