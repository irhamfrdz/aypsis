<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TagihanLoloBatamItem extends Model
{
    use HasFactory;

    protected $table = 'tagihan_lolo_batam_items';

    protected $fillable = [
        'tagihan_lolo_batam_id',
        'sumber_data',
        'surat_jalan_bongkaran_id',
        'langsir_batam_id',
        'master_pricelist_lolo_batam_id',
        'nomor_surat_jalan',
        'nomor_kontainer',
        'size',
        'tipe_kontainer',
        'kegiatan',
        'tarif',
        'jumlah',
        'total',
        'keterangan',
    ];

    protected $casts = [
        'tarif' => 'decimal:2',
        'jumlah' => 'integer',
        'total' => 'decimal:2',
    ];

    public function tagihanLoloBatam(): BelongsTo
    {
        return $this->belongsTo(TagihanLoloBatam::class, 'tagihan_lolo_batam_id');
    }

    public function suratJalanBongkaran(): BelongsTo
    {
        return $this->belongsTo(SuratJalanBongkaranBatam::class, 'surat_jalan_bongkaran_id');
    }

    public function langsirBatam(): BelongsTo
    {
        return $this->belongsTo(LangsirBatam::class, 'langsir_batam_id');
    }

    public function pricelistLoloBatam(): BelongsTo
    {
        return $this->belongsTo(MasterPricelistLoloBatam::class, 'master_pricelist_lolo_batam_id');
    }

    public function getFormattedTarifAttribute(): string
    {
        return 'Rp '.number_format($this->tarif, 0, ',', '.');
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp '.number_format($this->total, 0, ',', '.');
    }
}
