<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceAktivitasLainListrik extends Model
{
    use HasFactory;

    protected $table = 'invoice_aktivitas_lain_listrik';

    protected $fillable = [
        'invoice_aktivitas_lain_id',
        'referensi',
        'lwbp_baru',
        'lwbp_lama',
        'lwbp',
        'wbp',
        'lwbp_tarif',
        'wbp_tarif',
        'tarif_1',
        'tarif_2',
        'biaya_beban',
        'ppju',
        'dpp',
        'pph',
        'grand_total',
    ];

    protected $casts = [
        'lwbp_baru' => 'decimal:2',
        'lwbp_lama' => 'decimal:2',
        'lwbp' => 'decimal:2',
        'wbp' => 'decimal:2',
        'lwbp_tarif' => 'decimal:2',
        'wbp_tarif' => 'decimal:2',
        'tarif_1' => 'decimal:2',
        'tarif_2' => 'decimal:2',
        'biaya_beban' => 'decimal:2',
        'ppju' => 'decimal:2',
        'dpp' => 'decimal:2',
        'pph' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    /**
     * Relationship to Invoice Aktivitas Lain
     */
    public function invoiceAktivitasLain()
    {
        return $this->belongsTo(InvoiceAktivitasLain::class, 'invoice_aktivitas_lain_id');
    }
}
