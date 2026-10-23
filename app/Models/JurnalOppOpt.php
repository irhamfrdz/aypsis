<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JurnalOppOpt extends Model
{
    use Auditable;

    protected $table = 'jurnal_opp_opts';

    protected $fillable = [
        'tanggal',
        'nomor_referensi',
        'nama_kapal',
        'no_voyage',
        'nomor_kontainer',
        'nomor_bl',
        'vendor',
        'klasifikasi_biaya_id',
        'keterangan',
        'saldo',
        'kredit',
        'saldo_berjalan',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'saldo' => 'decimal:2',
        'kredit' => 'decimal:2',
        'saldo_berjalan' => 'decimal:2',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function klasifikasiBiaya(): BelongsTo
    {
        return $this->belongsTo(KlasifikasiBiaya::class, 'klasifikasi_biaya_id');
    }
}
