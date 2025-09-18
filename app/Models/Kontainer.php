<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kontainer extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'awalan_kontainer',
        'nomor_seri_kontainer',
        'akhiran_kontainer',
        'nomor_seri_gabungan',
        'ukuran',
        'tipe_kontainer',
        'tanggal_beli',
        'tanggal_jual',
        'keterangan',
        'kondisi_kontainer',
        'tanggal_kondisi_terakhir',
        'tanggal_masuk_sewa',
        'tanggal_selesai_sewa',
        'pemilik_kontainer',
        'tahun_pembuatan',
        'kontainer_asal',
        'keterangan1',
        'keterangan2',
        'status'
    ];

    /**
     * Cast date attributes to Carbon instances for safe formatting in views.
     *
     * @var array
     */
    protected $casts = [
        'tanggal_beli' => 'date',
        'tanggal_jual' => 'date',
        'tanggal_kondisi_terakhir' => 'date',
        'tanggal_masuk_sewa' => 'date',
        'tanggal_selesai_sewa' => 'date',
    ];

    // Relasi ke permohonan melalui pivot
    public function permohonans()
    {
        return $this->belongsToMany(Permohonan::class, 'permohonan_kontainers');
    }

    // Relasi ke perbaikan kontainer
    public function perbaikanKontainers()
    {
        return $this->hasMany(PerbaikanKontainer::class, 'nomor_kontainer', 'nomor_seri_gabungan');
    }

    // Accessor untuk nomor kontainer gabungan
    public function getNomorKontainerAttribute()
    {
        // Prefer the stored full serial if present so we display exactly what was
        // entered or discovered (`nomor_seri_gabungan`). Fall back to composing
        // from parts for older records.
        if (!empty($this->nomor_seri_gabungan)) {
            return $this->nomor_seri_gabungan;
        }
        return ($this->awalan_kontainer ?? '') . ($this->nomor_seri_kontainer ?? '') . ($this->akhiran_kontainer ?? '');
    }
}
