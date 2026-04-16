<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceAktivitasLainUtility extends Model
{
    protected $table = 'invoice_aktivitas_lain_utilities';

    protected $fillable = [
        'invoice_aktivitas_lain_id',
        'alat_berat_id',
        'referensi',
        'penerima',
        'tanggal',
        'jenis_tarif',
        'jumlah_periode',
        'tarif_satuan',
        'dpp',
        'pph',
        'ppn',
        'grand_total',
        'keterangan'
    ];

    public function invoiceAktivitasLain()
    {
        return $this->belongsTo(InvoiceAktivitasLain::class, 'invoice_aktivitas_lain_id');
    }

    public function alatBerat()
    {
        return $this->belongsTo(AlatBerat::class, 'alat_berat_id');
    }
}
