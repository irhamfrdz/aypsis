<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockAmprahanUsage extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'stock_amprahan_usages';

    protected $fillable = [
        'stock_amprahan_id',
        'penerima_id',
        'mobil_id',
        'kapal_id',
        'jumlah',
        'tanggal_pengambilan',
        'keterangan',
        'created_by',
    ];

    protected $dates = [
        'tanggal_pengambilan',
    ];

    public function stockAmprahan()
    {
        return $this->belongsTo(StockAmprahan::class);
    }

    public function penerima()
    {
        return $this->belongsTo(Karyawan::class, 'penerima_id');
    }

    public function mobil()
    {
        return $this->belongsTo(Mobil::class, 'mobil_id');
    }

    public function kapal()
    {
        return $this->belongsTo(MasterKapal::class, 'kapal_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
