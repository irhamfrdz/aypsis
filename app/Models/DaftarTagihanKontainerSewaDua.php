<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DaftarTagihanKontainerSewaDua extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'daftar_tagihan_kontainer_sewa_dua';

    protected $fillable = [
        'vendor',
        'nomor_kontainer',
        'size',
        'tanggal_awal',
        'tanggal_akhir',
        'group',
        'periode',
        'masa',
        'tarif',
        'status',
        'status_pembayaran',
        'nomor_invoice_vendor',
        'dpp',
        'adjustment',
        'adjustment_note',
        'dpp_nilai_lain',
        'ppn',
        'pph',
        'grand_total',
        'nomor_bank',
        'invoice_vendor',
        'tanggal_vendor',
        'pranota_id',
        'status_pranota',
        'invoice_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * Calculate taxes and totals based on current values
     */
    public function recalculateTaxes()
    {
        // Ensure values are float
        $dpp = (float) $this->dpp;
        $dpp_nilai_lain = (float) $this->dpp_nilai_lain;
        $ppn = (float) $this->ppn;
        $pph = (float) $this->pph;

        // Auto-calculate logic if needed (similar to controller update logic)
        // For now just ensure grand_total matches components
        $this->grand_total = round($dpp + $ppn - $pph, 2);
    }
}
