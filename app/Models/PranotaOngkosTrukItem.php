<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class PranotaOngkosTrukItem extends Model
{
    use HasFactory, Auditable;

    protected $table = 'pranota_ongkos_truk_items';

    protected $fillable = [
        'pranota_ongkos_truk_id',
        'surat_jalan_id',
        'surat_jalan_bongkaran_id',
        'no_surat_jalan',
        'tanggal',
        'nominal',
        'type',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function pranota()
    {
        return $this->belongsTo(PranotaOngkosTruk::class, 'pranota_ongkos_truk_id');
    }

    public function suratJalan()
    {
        return $this->belongsTo(SuratJalan::class, 'surat_jalan_id');
    }

    public function suratJalanBongkaran()
    {
        return $this->belongsTo(SuratJalanBongkaran::class, 'surat_jalan_bongkaran_id');
    }
}
