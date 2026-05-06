<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BtmSewaPermohonanTransfer extends Model
{
    use SoftDeletes;

    protected $table = 'btm_sewa_permohonan_transfers';

    protected $fillable = [
        'nomor',
        'tanggal',
        'vendor_name',
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
        return $this->hasMany(BtmSewaPermohonanTransferDetail::class, 'permohonan_id');
    }

    public function pranotas()
    {
        return $this->belongsToMany(BtmSewaPranota::class, 'btm_sewa_permohonan_transfer_details', 'permohonan_id', 'btm_sewa_pranota_id')
                    ->withPivot('subtotal');
    }

    public function payment()
    {
        return $this->hasOne(BtmSewaPayment::class, 'btm_sewa_permohonan_transfer_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
