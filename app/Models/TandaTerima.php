<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class TandaTerima extends Model
{
    use HasFactory, Auditable;
    protected $fillable = [
        'surat_jalan_id',
        'no_surat_jalan',
        'tanggal_surat_jalan',
        'supir',
        'supir_pengganti',
        'no_plat',
        'kegiatan',
        'jenis_barang',
        'tipe_kontainer',
        'tipe_kontainer_details',
        'size',
        'jumlah_kontainer',
        'no_kontainer',
        'kontainer_details',
        'no_seal',
        'tujuan_pengiriman',
        'pengirim',
        'penerima',
        'alamat_penerima',
        'gambar_checkpoint',
        'estimasi_nama_kapal',
        'nomor_ro',
        'expired_date',
        'nomor_performa',
        'tanggal',
        'tanggal_ambil_kontainer',
        'tanggal_checkpoint_supir',
        'tanggal_terima_pelabuhan',
        'tanggal_garasi',
        'jumlah',
        'satuan',
        'panjang',
        'lebar',
        'tinggi',
        'meter_kubik',
        'tonase',
        'dimensi_details',
        'nama_barang',
        'catatan',
        'lembur',
        'nginap',
        'term',
        'created_by',
        'updated_by',
        'dimensi_items',
    ];

    protected $casts = [
        'tanggal_surat_jalan' => 'date',
        'tanggal' => 'date',
        'tanggal_checkpoint_supir' => 'date',
        'tanggal_ambil_kontainer' => 'date',
        'tanggal_terima_pelabuhan' => 'date',
        'tanggal_garasi' => 'date',
        'expired_date' => 'date',
        'panjang' => 'decimal:3',
        'lebar' => 'decimal:3',
        'tinggi' => 'decimal:3',
        'meter_kubik' => 'decimal:3',
        'tonase' => 'decimal:3',
        'jumlah' => 'integer',
        'jumlah_kontainer' => 'integer',
        'dimensi_items' => 'array',
        'dimensi_details' => 'array',
        'kontainer_details' => 'array',
        'tipe_kontainer_details' => 'array',
        'nama_barang' => 'array',
        'lembur' => 'boolean',
        'nginap' => 'boolean',
    ];

    /**
     * Relasi ke Surat Jalan
     */
    public function suratJalan()
    {
        return $this->belongsTo(SuratJalan::class);
    }

    /**
     * Relasi ke User (created by)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke User (updated by)
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Relasi ke Prospek (one-to-many)
     */
    public function prospeks()
    {
        return $this->hasMany(\App\Models\Prospek::class, 'tanda_terima_id');
    }

    /**
     * Boot method untuk auto-linking
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-link prospek ketika TandaTerima dibuat atau diupdate
        static::created(function (self $tandaTerima) {
            $tandaTerima->autoLinkProspek();
        });

        static::updated(function (self $tandaTerima) {
            if ($tandaTerima->isDirty('surat_jalan_id') || $tandaTerima->isDirty('no_surat_jalan')) {
                $tandaTerima->autoLinkProspek();
            }
        });
    }

    /**
     * Auto-link prospek berdasarkan surat_jalan_id dan no_surat_jalan
     */
    public function autoLinkProspek()
    {
        // Cari prospek yang belum ter-link dan memiliki surat_jalan_id yang sama
        if ($this->surat_jalan_id) {
            \App\Models\Prospek::where('surat_jalan_id', $this->surat_jalan_id)
                ->whereNull('tanda_terima_id')
                ->update(['tanda_terima_id' => $this->id]);
        }

        // Alternatif: cari berdasarkan no_surat_jalan jika surat_jalan_id tidak ada
        if ($this->no_surat_jalan && !$this->surat_jalan_id) {
            \App\Models\Prospek::where('no_surat_jalan', $this->no_surat_jalan)
                ->whereNull('tanda_terima_id')
                ->update(['tanda_terima_id' => $this->id]);
        }
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeDateRange($query, $start, $end)
    {
        return $query->whereBetween('tanggal_surat_jalan', [$start, $end]);
    }

    /**
     * Format meter_kubik untuk tampilan (hilangkan trailing zeros)
     */
    public function getFormattedMeterKubikAttribute()
    {
        if (is_null($this->meter_kubik)) {
            return null;
        }
        return rtrim(rtrim(number_format($this->meter_kubik, 3, '.', ''), '0'), '.');
    }

    /**
     * Format tonase untuk tampilan (hilangkan trailing zeros)
     */
    public function getFormattedTonaseAttribute()
    {
        if (is_null($this->tonase)) {
            return null;
        }
        return rtrim(rtrim(number_format($this->tonase, 3, '.', ''), '0'), '.');
    }

    /**
     * Format panjang untuk tampilan (hilangkan trailing zeros)
     */
    public function getFormattedPanjangAttribute()
    {
        if (is_null($this->panjang)) {
            return null;
        }
        return rtrim(rtrim(number_format($this->panjang, 3, '.', ''), '0'), '.');
    }

    /**
     * Format lebar untuk tampilan (hilangkan trailing zeros)
     */
    public function getFormattedLebarAttribute()
    {
        if (is_null($this->lebar)) {
            return null;
        }
        return rtrim(rtrim(number_format($this->lebar, 3, '.', ''), '0'), '.');
    }

    /**
     * Format tinggi untuk tampilan (hilangkan trailing zeros)
     */
    public function getFormattedTinggiAttribute()
    {
        if (is_null($this->tinggi)) {
            return null;
        }
        return rtrim(rtrim(number_format($this->tinggi, 3, '.', ''), '0'), '.');
    }

    /**
     * Check if this tanda terima sudah masuk BL
     */
    public function sudahMasukBl()
    {
        // Cek apakah ada prospek yang terkait dengan tanda terima ini yang sudah punya BL
        return $this->prospeks()
            ->whereHas('bls')
            ->exists();
    }

    /**
     * Get semua BL yang terkait dengan tanda terima ini
     */
    public function getBls()
    {
        return \App\Models\Bl::whereIn('prospek_id', 
            $this->prospeks()->pluck('id')
        )->get();
    }
}
