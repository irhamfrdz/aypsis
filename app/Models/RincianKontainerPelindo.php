<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RincianKontainerPelindo extends Model
{
    use HasFactory;

    protected $table = 'rincian_kontainer_pelindos';

    protected $fillable = [
        'tanda_terima_id',
        'tanda_terima_tanpa_surat_jalan_id',
        'tanda_terima_lcl_id',
        'nomor_kontainer',
        'ukuran',
        'no_seal',
        'kegiatan',
        'estimasi_nama_kapal',
        'tanggal',
    ];

    public function tandaTerima()
    {
        return $this->belongsTo(TandaTerima::class, 'tanda_terima_id');
    }

    public function tandaTerimaTanpaSuratJalan()
    {
        return $this->belongsTo(TandaTerimaTanpaSuratJalan::class, 'tanda_terima_tanpa_surat_jalan_id');
    }

    public function tandaTerimaLcl()
    {
        return $this->belongsTo(TandaTerimaLcl::class, 'tanda_terima_lcl_id');
    }
}
