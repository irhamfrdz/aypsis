<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


use App\Traits\Auditable;
class Mobil extends Model
{
    use HasFactory;

    use Auditable;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mobils';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'kode_no',
        'nomor_polisi',
        'lokasi',
        'merek',
        'jenis',
        'tahun_pembuatan',
        'bpkb',
        'no_mesin',
        'nomor_rangka',
        'pajak_stnk',
        'pajak_plat',
        'no_kir',
        'pajak_kir',
        'atas_nama',
        'pemakai',
        'asuransi',
        'tanggal_jatuh_tempo_asuransi',
        'warna_plat',
        'catatan',
        'karyawan_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'pajak_stnk' => 'date',
        'pajak_plat' => 'date',
        'pajak_kir' => 'date',
        'tanggal_jatuh_tempo_asuransi' => 'date',
        'tahun_pembuatan' => 'integer',
    ];

    /**
     * Relationship with Karyawan model
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }
}
