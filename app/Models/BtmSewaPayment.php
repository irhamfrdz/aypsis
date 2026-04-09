<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BtmSewaPayment extends Model
{
    use SoftDeletes;

    protected $table = 'btm_sewa_payments';

    protected $fillable = [
        'nomor_pembayaran',
        'nomor_accurate',
        'tanggal_pembayaran',
        'bank',
        'jenis_transaksi',
        'total_pembayaran',
        'total_penyesuaian',
        'grand_total',
        'alasan_penyesuaian',
        'keterangan',
        'status',
        'created_by',
        'updated_by',
    ];

    public function details()
    {
        return $this->hasMany(BtmSewaPaymentDetail::class, 'btm_sewa_payment_id');
    }

    public function pranotas()
    {
        return $this->belongsToMany(BtmSewaPranota::class, 'btm_sewa_payment_details', 'btm_sewa_payment_id', 'btm_sewa_pranota_id')
                    ->withPivot('subtotal');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
