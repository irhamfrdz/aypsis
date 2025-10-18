<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterPricelistUangJalan extends Model
{
    use HasFactory;

    protected $table = 'master_pricelist_uang_jalan';

    protected $fillable = [
        'kode',
        'cabang',
        'wilayah',
        'dari',
        'ke',
        'uang_jalan_20ft',
        'uang_jalan_40ft',
        'keterangan',
        'liter',
        'jarak_km',
        'mel_20ft',
        'mel_40ft',
        'ongkos_truk_20ft',
        'antar_lokasi_20ft',
        'antar_lokasi_40ft',
        'status',
        'berlaku_dari',
        'berlaku_sampai',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'uang_jalan_20ft' => 'decimal:2',
        'uang_jalan_40ft' => 'decimal:2',
        'jarak_km' => 'decimal:2',
        'mel_20ft' => 'decimal:2',
        'mel_40ft' => 'decimal:2',
        'ongkos_truk_20ft' => 'decimal:2',
        'antar_lokasi_20ft' => 'decimal:2',
        'antar_lokasi_40ft' => 'decimal:2',
        'berlaku_dari' => 'date',
        'berlaku_sampai' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relationship dengan User (creator)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship dengan User (updater)
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope untuk filter berdasarkan status aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('berlaku_dari', '<=', now())
                    ->where(function ($q) {
                        $q->whereNull('berlaku_sampai')
                          ->orWhere('berlaku_sampai', '>=', now());
                    });
    }

    /**
     * Scope untuk filter berdasarkan cabang
     */
    public function scopeByCabang($query, $cabang)
    {
        return $query->where('cabang', $cabang);
    }

    /**
     * Scope untuk filter berdasarkan wilayah
     */
    public function scopeByWilayah($query, $wilayah)
    {
        return $query->where('wilayah', 'LIKE', "%{$wilayah}%");
    }

    /**
     * Get formatted uang jalan 20ft
     */
    public function getFormattedUangJalan20ftAttribute()
    {
        return 'Rp ' . number_format($this->uang_jalan_20ft, 0, ',', '.');
    }

    /**
     * Get formatted uang jalan 40ft
     */
    public function getFormattedUangJalan40ftAttribute()
    {
        return 'Rp ' . number_format($this->uang_jalan_40ft, 0, ',', '.');
    }

    /**
     * Get uang jalan berdasarkan ukuran kontainer
     */
    public function getUangJalanBySize($ukuran)
    {
        switch (strtoupper($ukuran)) {
            case '20':
            case '20FT':
            case '20FEET':
                return $this->uang_jalan_20ft;
            case '40':
            case '40FT':
            case '40FEET':
                return $this->uang_jalan_40ft;
            default:
                return 0;
        }
    }

    /**
     * Get mel tarif berdasarkan ukuran kontainer
     */
    public function getMelBySize($ukuran)
    {
        switch (strtoupper($ukuran)) {
            case '20':
            case '20FT':
            case '20FEET':
                return $this->mel_20ft;
            case '40':
            case '40FT':
            case '40FEET':
                return $this->mel_40ft;
            default:
                return 0;
        }
    }

    /**
     * Get antar lokasi tarif berdasarkan ukuran kontainer
     */
    public function getAntarLokasiBySize($ukuran)
    {
        switch (strtoupper($ukuran)) {
            case '20':
            case '20FT':
            case '20FEET':
                return $this->antar_lokasi_20ft;
            case '40':
            case '40FT':
            case '40FEET':
                return $this->antar_lokasi_40ft;
            default:
                return 0;
        }
    }

    /**
     * Cek apakah pricelist masih berlaku
     */
    public function isValid()
    {
        $now = now();

        if ($this->status !== 'active') {
            return false;
        }

        if ($this->berlaku_dari > $now) {
            return false;
        }

        if ($this->berlaku_sampai && $this->berlaku_sampai < $now) {
            return false;
        }

        return true;
    }

    /**
     * Get total biaya (uang jalan + mel + antar lokasi)
     */
    public function getTotalBiaya($ukuran)
    {
        return $this->getUangJalanBySize($ukuran) +
               $this->getMelBySize($ukuran) +
               $this->getAntarLokasiBySize($ukuran);
    }

    /**
     * Search berdasarkan rute (dari-ke)
     */
    public static function findByRoute($dari, $ke, $ukuran = null)
    {
        $query = static::active()
                      ->where('dari', 'LIKE', "%{$dari}%")
                      ->where('ke', 'LIKE', "%{$ke}%");

        if ($ukuran) {
            // Bisa ditambahkan filter berdasarkan ukuran jika diperlukan
        }

        return $query->first();
    }

    /**
     * Boot method untuk auto-generate kode
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->kode)) {
                $model->kode = static::generateKode($model->cabang);
            }
        });
    }

    /**
     * Generate kode otomatis
     */
    private static function generateKode($cabang)
    {
        $lastKode = static::where('cabang', $cabang)
                          ->orderBy('kode', 'desc')
                          ->first();

        if ($lastKode) {
            $lastNumber = (int) substr($lastKode->kode, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $cabang . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}
