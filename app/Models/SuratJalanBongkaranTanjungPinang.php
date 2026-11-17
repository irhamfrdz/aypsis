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
        'tanggal_surat_jalan', 'nomor_surat_jalan', 'nama_kapal', 'kapal_id', 'no_voyage', 'no_bl', 'bl_id',
        'pengirim', 'penerima', 'jenis_barang', 'tujuan_pengambilan', 'tujuan_pengiriman', 'supir', 'no_plat',
        'kenek', 'no_kontainer', 'no_seal', 'size', 'tipe_kontainer', 'term', 'aktifitas', 'input_by', 'input_date',
    ];

    protected $casts = [
        'tanggal_surat_jalan' => 'date',
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

    public function inputBy()
    {
        return $this->belongsTo(User::class, 'input_by');
    }
}
