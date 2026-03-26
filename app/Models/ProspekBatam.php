<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class ProspekBatam extends Model
{
    use HasFactory, Auditable;

    protected $table = 'prospek_batams';

    protected $fillable = [
        'tanggal',
        'nama_supir',
        'supir_ob',
        'barang',
        'pt_pengirim',
        'penerima',
        'ukuran',
        'tipe',
        'no_surat_jalan',
        'surat_jalan_batam_id',
        'tanda_terima_batam_id',
        'nomor_kontainer',
        'no_seal',
        'tujuan_pengiriman',
        'total_ton',
        'kuantitas',
        'total_volume',
        'nama_kapal',
        'kapal_id',
        'no_voyage',
        'pelabuhan_asal',
        'tanggal_muat',
        'keterangan',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tanggal_muat' => 'date',
        'total_ton' => 'decimal:3',
        'total_volume' => 'decimal:3',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Status constants
    const STATUS_AKTIF = 'aktif';
    const STATUS_SUDAH_MUAT = 'sudah_muat';
    const STATUS_BATAL = 'batal';

    public static function getStatusOptions()
    {
        return [
            self::STATUS_AKTIF => 'Aktif',
            self::STATUS_SUDAH_MUAT => 'Sudah Muat',
            self::STATUS_BATAL => 'Batal'
        ];
    }

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function tandaTerima()
    {
        return $this->belongsTo(\App\Models\TandaTerimaBatam::class, 'tanda_terima_batam_id');
    }

    public function suratJalan()
    {
        return $this->belongsTo(\App\Models\SuratJalanBatam::class, 'surat_jalan_batam_id');
    }

    public function kapal()
    {
        return $this->belongsTo(\App\Models\MasterKapal::class, 'kapal_id');
    }

    /**
     * Boot method untuk auto-linking
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-link dengan TandaTerimaBatam ketika ProspekBatam dibuat atau diupdate
        static::created(function (self $prospek) {
            $prospek->autoLinkTandaTerima();
        });

        static::updated(function (self $prospek) {
            if (($prospek->isDirty('surat_jalan_batam_id') || $prospek->isDirty('no_surat_jalan')) && !$prospek->tanda_terima_batam_id) {
                $prospek->autoLinkTandaTerima();
            }
        });
    }

    /**
     * Auto-link dengan TandaTerimaBatam berdasarkan surat_jalan_batam_id dan no_surat_jalan
     */
    public function autoLinkTandaTerima()
    {
        if ($this->tanda_terima_batam_id) {
            return; // Sudah ter-link
        }

        $tandaTerima = null;

        // Cari berdasarkan surat_jalan_batam_id terlebih dahulu
        if ($this->surat_jalan_batam_id) {
            $tandaTerima = \App\Models\TandaTerimaBatam::where('surat_jalan_batam_id', $this->surat_jalan_batam_id)->first();
        }

        // Jika tidak ditemukan, cari berdasarkan no_surat_jalan
        if (!$tandaTerima && $this->no_surat_jalan) {
            $tandaTerima = \App\Models\TandaTerimaBatam::where('no_surat_jalan', $this->no_surat_jalan)->first();
        }

        // Update jika ditemukan
        if ($tandaTerima) {
            $this->update(['tanda_terima_batam_id' => $tandaTerima->id]);
        }
    }

    public function bls()
    {
        // Untuk Batam, BL mungkin akan menggunakan prospek_batam_id nanti
        return $this->hasMany(Bl::class, 'prospek_batam_id');
    }
}
