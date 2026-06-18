<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SewaTagihan extends Model
{
    protected $table = 'sewa_tagihans';

    protected $primaryKey = 'id_tagihan';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id_tagihan',
        'id_sewa',
        'bulan_ke',
        'tanggal_awal',
        'tanggal_akhir',
        'jumlah_hari',
        'tipe_tarif',
        'jumlah_tagihan',
        'status_bayar',
        'tanggal_tagihan',
        'tanggal_bayar',
        'nomor_invoice_grup',
        'jumlah_tagihan_override',
        'jumlah_bayar',
        'selisih_pembayaran',
        'keterangan_selisih',
        'ppn',
        'pph',
        'nomor_bayar',
    ];

    public function transaksi()
    {
        return $this->belongsTo(SewaTransaksi::class, 'id_sewa', 'id_sewa');
    }

    public function invoice()
    {
        return $this->belongsTo(SewaInvoice::class, 'nomor_invoice_grup', 'nomor_invoice');
    }
}
