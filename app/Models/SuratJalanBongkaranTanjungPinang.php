<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratJalanBongkaranTanjungPinang extends Model
{
    use Auditable, HasFactory;

    protected $table = 'surat_jalan_bongkaran_tanjung_pinangs';

    protected $fillable = [
        'tanggal_surat_jalan', 'nomor_surat_jalan', 'nama_kapal', 'kapal_id', 'no_voyage', 'no_bl', 'bl_id', 'manifest_id',
        'pengirim', 'penerima', 'jenis_barang',
        'no_kontainer', 'no_seal', 'size', 'tipe_kontainer', 'term', 'uang_jalan', 'aktifitas', 'input_by', 'input_date',
    ];

    protected $casts = [
        'tanggal_surat_jalan' => 'date',
        'uang_jalan' => 'decimal:2',
        'input_date' => 'datetime',
    ];

    public function kapal()
    {
        return $this->belongsTo(MasterKapal::class, 'kapal_id');
    }

    public function bl()
    {
        return $this->belongsTo(Bl::class, 'bl_id');
    }

    public function manifest()
    {
        return $this->belongsTo(Manifest::class, 'manifest_id');
    }

    public function inputBy()
    {
        return $this->belongsTo(User::class, 'input_by');
    }
}
