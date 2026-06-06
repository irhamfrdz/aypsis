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
}
