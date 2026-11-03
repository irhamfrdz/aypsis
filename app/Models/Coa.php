<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coa extends Model
{
    use Auditable;

    public function isPiutangLainLain(): bool
    {
        return self::isPiutangLainLainName($this->nama_akun);
    }

    public static function isPiutangLainLainName(?string $namaAkun): bool
    {
        return (bool) preg_match('/\bpiutang[\s-]+lain[\s-]+lain\b/i', $namaAkun ?? '');
    }

    protected $table = 'akun_coa';

    protected $fillable = [
        'nomor_akun',
        'kode_nomor',
        'nama_akun',
        'tipe_akun',
        'saldo',
    ];

    protected $casts = [
        'saldo' => 'decimal:2',
    ];

    /**
     * Relasi ke transaksi COA
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(CoaTransaction::class, 'coa_id');
    }
}
