<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaKapalTemas extends Model
{
    protected $table = 'biaya_kapal_temas';

    protected $fillable = [
        'biaya_kapal_id',
        'temas_stage_id',
        'kapal',
        'voyage',
        'nomor_kontainer',
        'nomor_bl',
        'bl_id',
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
        'biaya_admin',
        'grand_total',
        'penerima',
        'nomor_rekening',
        'tanggal_invoice_vendor',
        'biaya_materai',
        'keterangan',
        'is_muat',
        'is_bongkar',
        'is_per_container',
    ];

    protected $casts = [
        'bl_id' => 'integer',
        'kuantitas' => 'decimal:2',
        'harga' => 'decimal:2',
        'sub_total' => 'decimal:2',
        'pph' => 'decimal:2',
        'ppn' => 'decimal:2',
        'pph_active' => 'boolean',
        'ppn_active' => 'boolean',
        'adjustment' => 'decimal:2',
        'biaya_admin' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'biaya_materai' => 'decimal:2',
        'tanggal_invoice_vendor' => 'date',
        'is_muat' => 'boolean',
        'is_bongkar' => 'boolean',
        'is_per_container' => 'boolean',
    ];

    /**
     * Relationship to BiayaKapal
     */
    public function biayaKapal()
    {
        return $this->belongsTo(BiayaKapal::class, 'biaya_kapal_id');
    }

    public function stage()
    {
        return $this->belongsTo(BiayaKapalTemasStage::class, 'temas_stage_id');
    }

    /** Cost assigned to this invoice row, including the portion paid using DP. */
    public function getRekapTotalAttribute(): float
    {
        if (! $this->temas_stage_id || ! $this->stage) {
            return (float) $this->grand_total;
        }
        if ($this->stage->payment_mode === 'dp') {
            return 0;
        }

        // Allocate the full invoice amount, independent of cash paid or a BL filter.
        $rows = $this->stage->details->sortBy('id');
        $subtotal = $rows->sum(fn ($row) => (int) round((float) $row->sub_total * 100));
        $total = (int) round((float) $this->stage->nilai_tagihan * 100);
        $cumulative = 0;
        $allocated = 0;
        foreach ($rows as $row) {
            $cumulative += (int) round((float) $row->sub_total * 100);
            $target = $subtotal > 0 ? (int) round($total * ($cumulative / $subtotal)) : $total;
            if ($row->id === $this->id) {
                return ($target - $allocated) / 100;
            }
            $allocated = $target;
        }

        return 0;
    }

    /**
     * Accessor for formatted sub_total
     */
    public function getFormattedSubTotalAttribute()
    {
        return 'Rp '.number_format((float) $this->sub_total, 0, ',', '.');
    }

    /**
     * Accessor for formatted grand_total
     */
    public function getFormattedGrandTotalAttribute()
    {
        return 'Rp '.number_format((float) $this->grand_total, 0, ',', '.');
    }

    /**
     * Accessor for formatted pph
     */
    public function getFormattedPphAttribute()
    {
        return 'Rp '.number_format((float) $this->pph, 0, ',', '.');
    }

    /**
     * Accessor for formatted ppn
     */
    public function getFormattedPpnAttribute()
    {
        return 'Rp '.number_format((float) $this->ppn, 0, ',', '.');
    }

    /**
     * Accessor for formatted adjustment
     */
    public function getFormattedAdjustmentAttribute()
    {
        return 'Rp '.number_format((float) $this->adjustment, 0, ',', '.');
    }
}
