<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockBanDalamUsage extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function stockBanDalam()
    {
        return $this->belongsTo(StockBanDalam::class);
    }

    public function mobil()
    {
        return $this->belongsTo(Mobil::class);
    }

    public function penerima()
    {
        return $this->belongsTo(Karyawan::class, 'penerima_id');
    }

    public function kapal()
    {
        return $this->belongsTo(MasterKapal::class, 'kapal_id');
    }

    public function gudang()
    {
        return $this->belongsTo(MasterGudangBan::class, 'gudang_id');
    }
}
