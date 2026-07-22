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
        'penerima',
        'bank_id',
        'virtual_account',
        'tanggal',
        'akun_coa_id',
        'tipe_transaksi',
        'nominal_debit',
        'nominal_kredit',
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
        'adjustment',
        'grand_total',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nominal_debit' => 'float',
        'nominal_kredit' => 'float',
        'lwbp_baru' => 'float',
        'lwbp_lama' => 'float',
        'lwbp' => 'float',
        'wbp' => 'float',
        'lwbp_tarif' => 'float',
        'wbp_tarif' => 'float',
        'tarif_1' => 'float',
        'tarif_2' => 'float',
        'biaya_beban' => 'float',
        'ppju' => 'float',
        'dpp' => 'float',
        'pph' => 'float',
        'adjustment' => 'float',
        'grand_total' => 'float',
    ];

    public function akunCoa()
    {
        return $this->belongsTo(\App\Models\AkunCoa::class, 'akun_coa_id');
    }

    /**
     * Relationship to Invoice Aktivitas Lain
     */
    public function invoiceAktivitasLain()
    {
        return $this->belongsTo(InvoiceAktivitasLain::class, 'invoice_aktivitas_lain_id');
    }

    public function bank()
    {
        return $this->belongsTo(\App\Models\Bank::class, 'bank_id');
    }
}
