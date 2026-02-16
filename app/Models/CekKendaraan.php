<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class CekKendaraan extends Model
{
    use HasFactory, Auditable;

    protected $table = 'cek_kendaraans';

    protected $fillable = [
        'karyawan_id',
        'mobil_id',
        'tanggal',
        'jam',
        'oli_mesin',
        'air_radiator',
        'minyak_rem',
        'air_wiper',
        'lampu_depan',
        'lampu_belakang',
        'lampu_sein',
        'lampu_rem',
        'kondisi_ban',
        'tekanan_ban',
        'aki',
        'fungsi_rem',
        'fungsi_kopling',
        'kebersihan_interior',
        'kebersihan_eksterior',
        'kotak_p3k',
        'plat_no_belakang',
        'lampu_besar_dekat_kanan',
        'odometer',
        'masa_berlaku_stnk',
        'masa_berlaku_kir',
        'catatan',
        'foto_sebelum',
        'foto_sesudah',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam' => 'string', // time is usually cast differently but string is okay for display
        'odometer' => 'integer',
        'masa_berlaku_stnk' => 'date',
        'masa_berlaku_kir' => 'date',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    public function mobil()
    {
        return $this->belongsTo(Mobil::class, 'mobil_id');
    }
}
