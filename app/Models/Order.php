<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'nomor_order',
        'tanggal_order',
        'tujuan_kirim',
        'no_tiket_do',
        'tujuan_ambil',
        'tujuan_ambil_id',
        'size_kontainer',
        'unit_kontainer',
        'tipe_kontainer',
        'tanggal_pickup',
        'exclude_ftz03',
        'include_ftz03',
        'exclude_sppb',
        'include_sppb',
        'exclude_buruh_bongkar',
        'include_buruh_bongkar',
        'term_id',
        'pengirim_id',
        'jenis_barang_id',
        'status',
        'catatan'
    ];

    protected $casts = [
        'tanggal_order' => 'date',
        'tanggal_pickup' => 'date',
        'exclude_ftz03' => 'boolean',
        'include_ftz03' => 'boolean',
        'exclude_sppb' => 'boolean',
        'include_sppb' => 'boolean',
        'exclude_buruh_bongkar' => 'boolean',
        'include_buruh_bongkar' => 'boolean',
    ];

    // Relationships
    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function pengirim(): BelongsTo
    {
        return $this->belongsTo(Pengirim::class);
    }

    public function jenisBarang(): BelongsTo
    {
        return $this->belongsTo(JenisBarang::class, 'jenis_barang_id');
    }

    public function tujuanAmbil(): BelongsTo
    {
        return $this->belongsTo(TujuanKegiatanUtama::class, 'tujuan_ambil_id');
    }
}
