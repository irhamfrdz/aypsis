<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaKapalBuruhBongkar extends Model
{
    protected $fillable = [
        'biaya_kapal_id',
        'kapal',
        'voyage',
        'kontainer_ids',
        'nominal',
        'adjustment',
        'notes_adjustment',
        'pph_percent',
        'pph_amount',
        'penerima_bukti_potong_pph_id',
        'npwp',
        'total_nominal',
        'nomor_bukti',
        'penerima',
        'master_customer_buruh_id',
        'bank_id',
        'nomor_rekening',
    ];

    protected $casts = [
        'kontainer_ids' => 'array',
        'nominal' => 'decimal:2',
        'adjustment' => 'decimal:2',
        'pph_percent' => 'decimal:2',
        'pph_amount' => 'decimal:2',
        'total_nominal' => 'decimal:2',
    ];

    public function biayaKapal()
    {
        return $this->belongsTo(BiayaKapal::class, 'biaya_kapal_id');
    }

    public function masterCustomerBuruh()
    {
        return $this->belongsTo(MasterCustomerBuruh::class, 'master_customer_buruh_id');
    }

    public function penerimaBuktiPotongPph()
    {
        return $this->belongsTo(PenerimaBuktiPotongPph::class, 'penerima_bukti_potong_pph_id');
    }
}
