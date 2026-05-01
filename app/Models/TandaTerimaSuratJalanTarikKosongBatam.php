<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TandaTerimaSuratJalanTarikKosongBatam extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'surat_jalan_tarik_kosong_batam_id',
        'no_tanda_terima',
        'tanggal_tanda_terima',
        'no_surat_jalan',
        'tanggal_surat_jalan',
        'supir',
        'no_plat',
        'no_kontainer',
        'size',
        'penerima',
        'catatan',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_tanda_terima' => 'date',
        'tanggal_surat_jalan' => 'date',
    ];

    public function suratJalan()
    {
        return $this->belongsTo(SuratJalanTarikKosongBatam::class, 'surat_jalan_tarik_kosong_batam_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
