<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GajiSupirBatam extends Model
{
    use Auditable, HasFactory;

    protected $table = 'gaji_supir_batams';

    protected $fillable = [
        'karyawan_id',
        'periode_bulan',
        'periode_tahun',
        'gaji_pokok',
        'tunjangan_kehadiran',
        'tunjangan_makan',
        'tunjangan_lainnya',
        'potongan_bpjs',
        'potongan_pinjaman',
        'potongan_lainnya',
        'total_gaji',
        'status_pembayaran',
        'tanggal_dibayar',
        'keterangan',
    ];

    protected $casts = [
        'gaji_pokok' => 'decimal:2',
        'tunjangan_kehadiran' => 'decimal:2',
        'tunjangan_makan' => 'decimal:2',
        'tunjangan_lainnya' => 'decimal:2',
        'potongan_bpjs' => 'decimal:2',
        'potongan_pinjaman' => 'decimal:2',
        'potongan_lainnya' => 'decimal:2',
        'total_gaji' => 'decimal:2',
        'tanggal_dibayar' => 'date',
    ];

    /**
     * Relationship to the driver (karyawan)
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }
}
