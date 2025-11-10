<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class TagihanOb extends Model
{
    use HasFactory, Auditable;

    protected $table = 'tagihan_ob';

    protected $fillable = [
        'kapal',
        'voyage',
        'nomor_kontainer',
        'nama_supir',
        'barang',
        'status_kontainer', // full atau empty
        'biaya',
        'size_kontainer',
        'bl_id', // untuk referensi ke BL
        'created_by',
        'keterangan'
    ];

    protected $casts = [
        'biaya' => 'decimal:2',
    ];

    /**
     * Get the BL that owns this tagihan OB
     */
    public function bl()
    {
        return $this->belongsTo(\App\Models\Bl::class, 'bl_id');
    }

    /**
     * Get the user who created this tagihan OB
     */
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /**
     * Accessor for formatted biaya
     */
    public function getFormattedBiayaAttribute()
    {
        return 'Rp ' . number_format($this->biaya, 0, ',', '.');
    }

    /**
     * Accessor for status kontainer label
     */
    public function getStatusKontainerLabelAttribute()
    {
        return $this->status_kontainer === 'full' ? 'Full' : 'Empty';
    }

    /**
     * Get status kontainer based on surat jalan kegiatan
     * 
     * @param string $kegiatan
     * @return string
     */
    public static function getStatusKontainerFromKegiatan($kegiatan)
    {
        // Berdasarkan requirement: 
        // - jika kegiatannya tarik isi maka full
        // - jika tarik kosong maka empty (E)
        
        if (str_contains(strtolower($kegiatan), 'tarik isi') || str_contains(strtolower($kegiatan), 'muat')) {
            return 'full';
        } elseif (str_contains(strtolower($kegiatan), 'tarik kosong') || str_contains(strtolower($kegiatan), 'bongkar')) {
            return 'empty';
        }
        
        // Default to empty if unclear
        return 'empty';
    }

    /**
     * Calculate biaya from master pricelist OB
     * 
     * @param string $sizeKontainer
     * @param string $statusKontainer
     * @return float
     */
    public static function calculateBiayaFromPricelist($sizeKontainer, $statusKontainer)
    {
        $pricelist = \App\Models\MasterPricelistOb::where('size_kontainer', $sizeKontainer)
                                                  ->where('status_kontainer', $statusKontainer)
                                                  ->first();
        
        return $pricelist ? $pricelist->biaya : 0;
    }
}