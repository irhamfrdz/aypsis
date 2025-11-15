<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class PembayaranUangMukaSupirDetail extends Model
{
    use Auditable;

    protected $table = 'pembayaran_uang_muka_supir_details';

    protected $fillable = [
        'pembayaran_id',
        'nama_supir',
        'jumlah_uang_muka',
        'keterangan',
        'status',
    ];

    protected $casts = [
        'jumlah_uang_muka' => 'decimal:2',
    ];

    /**
     * Relasi ke pembayaran aktivitas lainnya
     */
    public function pembayaran()
    {
        return $this->belongsTo(PembayaranAktivitasLainnya::class, 'pembayaran_id');
    }

    /**
     * Relasi ke karyawan (supir)
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'nama_supir', 'nama_lengkap');
    }
}
