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
        'kotak_p3k',
        'racun_api',
        'plat_no_depan',
        'plat_no_belakang',
        'lampu_jauh_kanan',
        'lampu_jauh_kiri',
        'lampu_dekat_kanan',
        'lampu_dekat_kiri',
        'lampu_sein_depan_kanan',
        'lampu_sein_depan_kiri',
        'lampu_sein_belakang_kanan',
        'lampu_sein_belakang_kiri',
        'lampu_rem_kanan',
        'lampu_rem_kiri',
        'lampu_mundur_kanan',
        'lampu_mundur_kiri',
        'sabuk_pengaman_kanan',
        'sabuk_pengaman_kiri',
        'kamvas_rem_depan_kanan',
        'kamvas_rem_depan_kiri',
        'kamvas_rem_belakang_kanan',
        'kamvas_rem_belakang_kiri',
        'spion_kanan',
        'spion_kiri',
        'tekanan_ban_depan_kanan',
        'tekanan_ban_depan_kiri',
        'tekanan_ban_belakang_kanan',
        'tekanan_ban_belakang_kiri',
        'ganjelan_ban',
        'trakel_sabuk',
        'twist_lock_kontainer',
        'landing_buntut',
        'patok_besi',
        'tutup_tangki',
        'lampu_no_plat',
        'lampu_bahaya',
        'klakson',
        'radio',
        'rem_tangan',
        'pedal_gas',
        'pedal_rem',
        'porseneling',
        'antena_radio',
        'speaker',
        'spion_dalam',
        'dongkrak',
        'tangkai_dongkrak',
        'kunci_roda',
        'dop_roda',
        'wiper_depan',
        'oli_mesin',
        'air_radiator',
        'minyak_rem',
        'air_wiper',
        'kondisi_aki',
        'pengukur_tekanan_ban',
        'segitiga_pengaman',
        'jumlah_ban_serep',
        'odometer',
        'masa_berlaku_stnk',
        'masa_berlaku_kir',
        'nomor_sim',
        'masa_berlaku_sim_start',
        'masa_berlaku_sim_end',
        'catatan',
        'pernyataan',
        'foto_sebelum',
        'foto_sesudah',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam' => 'string', // time is usually cast differently but string is okay for display
        'odometer' => 'integer',
        'masa_berlaku_stnk' => 'date',
        'masa_berlaku_kir' => 'date',
        'masa_berlaku_sim_start' => 'date',
        'masa_berlaku_sim_end' => 'date',
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
