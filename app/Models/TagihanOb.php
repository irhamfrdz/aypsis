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
        'kegiatan', // muat atau bongkar
        'nomor_kontainer',
        'nama_supir',
        'barang',
        'status_kontainer', // full atau empty
        'biaya',
        'dp',
        'size_kontainer',
        'bl_id', // untuk referensi ke BL (untuk OB Bongkar)
        'naik_kapal_id', // untuk referensi ke Naik Kapal (untuk OB Muat)
        'created_by',
        'keterangan'
    ];

    protected $casts = [
        'biaya' => 'float',
        'dp' => 'float',
    ];

    /**
     * Get the BL that owns this tagihan OB (untuk OB Bongkar)
     */
    public function bl()
    {
        return $this->belongsTo(\App\Models\Bl::class, 'bl_id');
    }

    /**
     * Get the Naik Kapal that owns this tagihan OB (untuk OB Muat)
     */
    public function naikKapal()
    {
        return $this->belongsTo(\App\Models\NaikKapal::class, 'naik_kapal_id');
    }

    /**
     * Get the user who created this tagihan OB
     */
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /**
     * Get pranota item untuk tagihan OB ini
     */
    public function pranotaObItem()
    {
        // The pranota_ob_items stores item_type as model class, and item_id as the id
        return $this->hasOne(PranotaObItem::class, 'item_id', 'id')->where('item_type', self::class);
    }

    /**
     * Check apakah tagihan OB sudah ada di pranota
     */
    public function isInPranota(): bool
    {
        return $this->pranotaObItem()->exists();
    }

    /**
     * Get pranota yang berisi tagihan OB ini
     */
    public function pranota()
    {
        return $this->pranotaObItem?->pranotaOb;
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

    /**
     * Mutator to normalize nomor_kontainer when saving to DB
     */
    public function setNomorKontainerAttribute($value)
    {
        if ($value === null) {
            $this->attributes['nomor_kontainer'] = null;
            return;
        }

        $normalized = trim((string) $value);
        // store as-is with trimmed spaces; upper/lowercase will be handled in queries
        $this->attributes['nomor_kontainer'] = $normalized;
    }
}