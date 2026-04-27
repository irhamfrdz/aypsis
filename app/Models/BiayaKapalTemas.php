<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class BiayaKapalTemas extends Model
{
    protected $table = 'biaya_kapal_temas';
 
    protected $fillable = [
        'biaya_kapal_id',
        'kapal',
        'voyage',
        'nomor_referensi',
        'pricelist_temas_id',
        'jenis_biaya',
        'lokasi',
        'size',
        'kuantitas',
        'harga',
        'sub_total',
        'pph',
        'ppn',
        'pph_active',
        'ppn_active',
        'adjustment',
        'grand_total',
        'penerima',
        'nomor_rekening',
        'tanggal_invoice_vendor',
        'biaya_materai',
        'keterangan',
        'is_muat',
        'is_bongkar',
    ];
 
    protected $casts = [
        'kuantitas' => 'decimal:2',
        'harga' => 'decimal:2',
        'sub_total' => 'decimal:2',
        'pph' => 'decimal:2',
        'ppn' => 'decimal:2',
        'pph_active' => 'boolean',
        'ppn_active' => 'boolean',
        'adjustment' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'biaya_materai' => 'decimal:2',
        'tanggal_invoice_vendor' => 'date',
        'is_muat' => 'boolean',
        'is_bongkar' => 'boolean',
    ];
 
    /**
     * Relationship to BiayaKapal
     */
    public function biayaKapal()
    {
        return $this->belongsTo(BiayaKapal::class, 'biaya_kapal_id');
    }
 
    /**
     * Accessor for formatted sub_total
     */
    public function getFormattedSubTotalAttribute()
    {
        return 'Rp ' . number_format((float) $this->sub_total, 0, ',', '.');
    }
 
    /**
     * Accessor for formatted grand_total
     */
    public function getFormattedGrandTotalAttribute()
    {
        return 'Rp ' . number_format((float) $this->grand_total, 0, ',', '.');
    }
 
    /**
     * Accessor for formatted pph
     */
    public function getFormattedPphAttribute()
    {
        return 'Rp ' . number_format((float) $this->pph, 0, ',', '.');
    }
 
    /**
     * Accessor for formatted ppn
     */
    public function getFormattedPpnAttribute()
    {
        return 'Rp ' . number_format((float) $this->ppn, 0, ',', '.');
    }
 
    /**
     * Accessor for formatted adjustment
     */
    public function getFormattedAdjustmentAttribute()
    {
        return 'Rp ' . number_format((float) $this->adjustment, 0, ',', '.');
    }
}
