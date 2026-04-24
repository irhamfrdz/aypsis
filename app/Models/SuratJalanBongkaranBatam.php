<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use App\Traits\Auditable;
use App\Models\MasterKapal;

class SuratJalanBongkaranBatam extends Model
{
    use HasFactory, Auditable;

    protected $table = 'surat_jalan_bongkaran_batams';

    protected $fillable = [
        'tanggal_surat_jalan',
        'lanjut_muat',
        'nomor_sj_sebelumnya',
        'nomor_surat_jalan',
        'kegiatan',
        'pengirim',
        'penerima',
        'jenis_barang',
        'tujuan_alamat',
        'telp',
        'tujuan_pengambilan',
        'retur_barang',
        'jumlah_retur',
        'karyawan',
        'supir',
        'supir2',
        'no_plat',
        'kenek',
        'krani',
        'tipe_kontainer',
        'no_kontainer',
        'no_seal',
        'size',
        'jumlah_kontainer',
        'karton',
        'plastik',
        'terpal',
        'waktu_berangkat',
        'tujuan_pengiriman',
        'tanggal_muat',
        'jam_berangkat',
        'term',
        'rit',
        'uang_jalan',
        'uang_jalan_type',
        'uang_jalan_nominal',
        'tagihan_ayp',
        'tagihan_atb',
        'tagihan_pb',
        'no_pemesanan',
        'gambar',
        'gambar_checkpoint',
        'input_by',
        'input_date',
        'status',
        'status_pembayaran',
        'status_pembayaran_uang_rit',
        'status_pembayaran_uang_rit_kenek',
        'total_tarif',
        'jumlah_terbayar',
        'aktifitas',
        'nama_kapal',
        'kapal_id',
        'no_voyage',
        'no_bl',
        'bl_id',
        'manifest_id',
        'jenis_pengiriman',
        'tanggal_ambil_barang',
        'lokasi',
        'f_e',
        'lembur',
        'nginap',
        'tidak_lembur_nginap'
    ];

    protected $casts = [
        'tanggal_surat_jalan' => 'date',
        'tanggal_muat' => 'date',
        'tanggal_ambil_barang' => 'date',
        'input_date' => 'datetime',
        'waktu_berangkat' => 'datetime',
        'uang_jalan' => 'decimal:2',
        'uang_jalan_nominal' => 'decimal:2',
        'total_tarif' => 'decimal:2',
        'jumlah_terbayar' => 'decimal:2',
        'jumlah_retur' => 'integer',
        'jumlah_kontainer' => 'integer',
        'lembur' => 'boolean',
        'nginap' => 'boolean',
        'tidak_lembur_nginap' => 'boolean',
        'lanjut_muat' => 'boolean',
    ];

    // Relationships
    public function inputBy()
    {
        return $this->belongsTo(User::class, 'input_by');
    }

    public function kapal()
    {
        return $this->belongsTo(MasterKapal::class, 'kapal_id');
    }

    public function bl()
    {
        return $this->belongsTo(\App\Models\Bl::class, 'bl_id');
    }

    public function manifest()
    {
        return $this->belongsTo(Manifest::class, 'manifest_id');
    }

    /**
     * Relationship to Tanda Terima Bongkaran Batam
     */
    public function tandaTerima()
    {
        return $this->hasOne(\App\Models\TandaTerimaBongkaranBatam::class, 'surat_jalan_bongkaran_id');
    }

    /**
     * Relationship to Karyawan as Supir
     */
    public function supirKaryawan()
    {
        return $this->belongsTo(Karyawan::class, 'supir', 'nama_panggilan');
    }

    /**
     * Relationship to Karyawan as Supir 2
     */
    public function supir2Karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'supir2', 'nama_panggilan');
    }

    /**
     * Relationship to Karyawan as Kenek
     */
    public function kenekKaryawan()
    {
        return $this->belongsTo(Karyawan::class, 'kenek', 'nama_lengkap');
    }

    // Accessors & Mutators
    public function getFormattedTanggalSuratJalanAttribute()
    {
        return $this->tanggal_surat_jalan ? Carbon::parse($this->tanggal_surat_jalan)->format('d/m/Y') : null;
    }

    public function setTanggalSuratJalanAttribute($value)
    {
        $this->attributes['tanggal_surat_jalan'] = $value ? Carbon::parse($value)->format('Y-m-d') : null;
    }

    public function setTanggalMuatAttribute($value)
    {
        $this->attributes['tanggal_muat'] = $value ? Carbon::parse($value)->format('Y-m-d') : null;
    }
}
