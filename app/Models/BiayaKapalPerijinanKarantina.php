<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaKapalPerijinanKarantina extends Model
{
    protected $table = 'biaya_kapal_perijinan_karantinas';

    protected $fillable = [
        'biaya_kapal_perijinan_id',
        'source_type',
        'source_id',
        'nomor_dokumen',
        'nominal',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
    ];

    public function perijinan()
    {
        return $this->belongsTo(BiayaKapalPerijinan::class, 'biaya_kapal_perijinan_id');
    }

    public function getSourceAttribute()
    {
        return match ($this->source_type) {
            'surat_jalan' => SuratJalan::find($this->source_id),
            'tanda_terima_tanpa_surat_jalan' => TandaTerimaTanpaSuratJalan::find($this->source_id),
            'tanda_terima_lcl' => TandaTerimaLcl::find($this->source_id),
            default => null,
        };
    }
}
