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
        'kegiatan',
        'jenis_barang',
        'tipe_kontainer',
        'size',
        'jumlah_kontainer',
        'no_kontainer',
        'no_seal',
        'tujuan_pengiriman',
        'pengirim',
        'gambar_checkpoint',
        'estimasi_nama_kapal',
        'tanggal_ambil_kontainer',
        'tanggal_terima_pelabuhan',
        'tanggal_garasi',
        'jumlah',
        'satuan',
        'panjang',
        'lebar',
        'tinggi',
        'meter_kubik',
        'tonase',
        'catatan',
        'created_by',
        'updated_by',
        'dimensi_items',
    ];

    protected $casts = [
        'tanggal_surat_jalan' => 'date',
        'tanggal_ambil_kontainer' => 'date',
        'tanggal_terima_pelabuhan' => 'date',
        'tanggal_garasi' => 'date',
        'panjang' => 'decimal:3',
        'lebar' => 'decimal:3',
        'tinggi' => 'decimal:3',
        'meter_kubik' => 'decimal:3',
        'tonase' => 'decimal:3',
        'jumlah' => 'integer',
        'jumlah_kontainer' => 'integer',
        'dimensi_items' => 'array',
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
}
