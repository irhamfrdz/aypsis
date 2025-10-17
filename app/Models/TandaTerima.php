<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TandaTerima extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'surat_jalan_id',
        'no_surat_jalan',
        'tanggal_surat_jalan',
        'supir',
        'kegiatan',
        'jenis_barang',
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
        'berat_kotor',
        'dimensi',
        'catatan',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_surat_jalan' => 'date',
        'tanggal_ambil_kontainer' => 'date',
        'tanggal_terima_pelabuhan' => 'date',
        'tanggal_garasi' => 'date',
        'berat_kotor' => 'decimal:2',
        'jumlah' => 'integer',
        'jumlah_kontainer' => 'integer',
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
     * Scope untuk filter berdasarkan status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeDateRange($query, $start, $end)
    {
        return $query->whereBetween('tanggal_surat_jalan', [$start, $end]);
    }
}
