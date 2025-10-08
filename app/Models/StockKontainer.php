<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockKontainer extends Model
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
        'status',
        'tanggal_masuk',
        'tanggal_keluar',
        'keterangan',
        'tahun_pembuatan'
    ];

    /**
     * Cast date attributes to Carbon instances for safe formatting in views.
     *
     * @var array
     */
    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_keluar' => 'date',
        'tahun_pembuatan' => 'integer',
    ];

    /**
     * Boot the model and add event listeners
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($stockKontainer) {
            self::validateNomorSeriUniqueness($stockKontainer);
        });

        static::updating(function ($stockKontainer) {
            self::validateNomorSeriUniqueness($stockKontainer);
        });
    }

    /**
     * Validate nomor seri kontainer uniqueness across stock_kontainers and kontainers
     */
    private static function validateNomorSeriUniqueness($stockKontainer)
    {
        if (empty($stockKontainer->nomor_seri_gabungan)) {
            return;
        }

        // Cek duplikasi dengan tabel kontainers
        $existingKontainer = \App\Models\Kontainer::where('nomor_seri_gabungan', $stockKontainer->nomor_seri_gabungan)
            ->where('status', 'active')
            ->first();

        if ($existingKontainer) {
            // Jika ada kontainer aktif dengan nomor yang sama, set stock kontainer ke inactive
            $stockKontainer->status = 'inactive';
        }

        // Cek duplikasi dengan stock_kontainers lain (selain diri sendiri)
        $query = self::where('nomor_seri_gabungan', $stockKontainer->nomor_seri_gabungan)
            ->where('status', 'active');
        
        if ($stockKontainer->exists) {
            $query->where('id', '!=', $stockKontainer->id);
        }

        $existingStock = $query->first();
        if ($existingStock) {
            // Jika ada stock kontainer aktif lain dengan nomor yang sama, set yang lama ke inactive
            $existingStock->update(['status' => 'inactive']);
        }
    }

    /**
     * Get the status badge color for the kontainer.
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'available' => 'bg-green-100 text-green-800',
            'rented' => 'bg-blue-100 text-blue-800',
            'maintenance' => 'bg-yellow-100 text-yellow-800',
            'damaged' => 'bg-red-100 text-red-800',
            'inactive' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }



    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk kontainer yang tersedia
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Scope untuk kontainer yang aktif (bukan inactive)
     */
    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'inactive');
    }

    /**
     * Check if this kontainer has duplicate in kontainers table
     */
    public function hasDuplicateInKontainers()
    {
        return \App\Models\Kontainer::where('nomor_seri_gabungan', $this->nomor_seri_gabungan)->exists();
    }

    /**
     * Accessor untuk nomor kontainer gabungan
     */
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
