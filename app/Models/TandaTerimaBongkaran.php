<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TandaTerimaBongkaran extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $table = 'tanda_terima_bongkarans';

    protected $fillable = [
        'nomor_tanda_terima',
        'tanggal_tanda_terima',
        'surat_jalan_bongkaran_id',
        'gudang_id',
        'no_kontainer',
        'no_seal',
        'kegiatan',
        'status',
        'keterangan',
        'lembur',
        'nginap',
        'tidak_lembur_nginap'
    ];

    protected $casts = [
        'tanggal_tanda_terima' => 'date',
        'lembur' => 'boolean',
        'nginap' => 'boolean',
        'tidak_lembur_nginap' => 'boolean',
    ];

    /**
     * Relationship to SuratJalanBongkaran
     */
    public function suratJalanBongkaran(): BelongsTo
    {
        return $this->belongsTo(SuratJalanBongkaran::class, 'surat_jalan_bongkaran_id');
    }

    /**
     * Relationship to Gudang
     */
    public function gudang(): BelongsTo
    {
        return $this->belongsTo(Gudang::class, 'gudang_id');
    }
}
