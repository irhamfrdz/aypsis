<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\MasterKapal;

class MasterDokumenKapalAlexindo extends Model
{
    protected $fillable = [
        'kapal_id',
        'nama_dokumen',
        'nomor_dokumen',
        'tanggal_terbit',
        'tanggal_berakhir',
        'file_dokumen',
        'keterangan',
    ];

    public function kapal()
    {
        return $this->belongsTo(MasterKapal::class, 'kapal_id');
    }
}
