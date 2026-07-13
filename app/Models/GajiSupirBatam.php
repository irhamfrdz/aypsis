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
        'periode_minggu',
        'tanggal_mulai',
        'tanggal_selesai',
        'gaji_pokok',
        'uang_malam_libur',
        'biaya_bensin',
        'is_potongan_5_persen',
        'nominal_potongan_5_persen',
        'total_gaji',
        'status_pembayaran',
        'tanggal_dibayar',
        'keterangan',
    ];

    protected $casts = [
        'periode_minggu' => 'integer',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'gaji_pokok' => 'decimal:2',
        'uang_malam_libur' => 'decimal:2',
        'biaya_bensin' => 'decimal:2',
        'is_potongan_5_persen' => 'boolean',
        'nominal_potongan_5_persen' => 'decimal:2',
        'total_gaji' => 'decimal:2',
        'tanggal_dibayar' => 'date',
    ];

    public function getPeriodeTextAttribute()
    {
        if ($this->tanggal_mulai && $this->tanggal_selesai) {
            return $this->tanggal_mulai->format('d/m/Y').' - '.$this->tanggal_selesai->format('d/m/Y');
        }

        return $this->periode_minggu == 2 ? 'Tanggal 16 - Akhir Bulan' : 'Tanggal 1 - 15';
    }

    /**
     * Relationship to the driver (karyawan)
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }
}
