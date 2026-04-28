<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratJalanTarikKosongBatam extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal_surat_jalan',
        'no_surat_jalan',
        'no_tiket_do',
        'pengirim',
        'penerima',
        'alamat',
        'tujuan_pengambilan',
        'tujuan_pengiriman',
        'supir',
        'supir2',
        'no_plat',
        'kenek',
        'tipe_kontainer',
        'no_kontainer',
        'size',
        'f_e',
        'uang_jalan',
        'status_pembayaran_uang_jalan',
        'input_by',
        'input_date',
        'lokasi',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_surat_jalan' => 'date',
        'input_date' => 'datetime',
        'uang_jalan' => 'decimal:2',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'input_by');
    }
}
