<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mobil extends Model
{
    use Auditable;
    use HasFactory;

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
        'nickname',
        'lokasi',
        'merek',
        'jenis',
        'roda',
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
        'is_asset_karyawan',
        'tanggal_tanda_terima_asset',
        'status_operasional',
        'kondisi_fisik',
        'kategori_asset',
        'imei_gps',
        'km',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_asset_karyawan' => 'boolean',
        'tanggal_tanda_terima_asset' => 'date',
        'km' => 'decimal:3',
        'pajak_stnk' => 'date',
        'pajak_plat' => 'date',
        'pajak_kir' => 'date',
        'tanggal_jatuh_tempo_asuransi' => 'date',
        'tahun_pembuatan' => 'integer',
        'roda' => 'integer',
    ];

    /**
     * Relationship with Karyawan model
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }
}
