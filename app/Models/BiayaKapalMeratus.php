<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class BiayaKapalMeratus extends Model
{
    protected $table = 'biaya_kapal_meratus';
 
    protected $fillable = [
        'biaya_kapal_id',
        'kapal',
        'voyage',
        'nomor_referensi',
        'pricelist_meratus_id',
        'jenis_biaya',
        'lokasi',
        'size',
        'kuantitas',
        'harga',
        'sub_total',
        'pph',
        'grand_total',
        'penerima',
        'nomor_rekening',
        'tanggal_invoice_vendor',
        'biaya_materai',
        'keterangan',
    ];
 
    protected $casts = [
        'kuantitas' => 'decimal:2',
        'harga' => 'decimal:2',
        'sub_total' => 'decimal:2',
        'pph' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'biaya_materai' => 'decimal:2',
        'tanggal_invoice_vendor' => 'date',
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
}
