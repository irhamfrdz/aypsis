<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockBanDalamUsage extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function stockBanDalam()
    {
        return $this->belongsTo(StockBanDalam::class);
    }

    public function mobil()
    {
        return $this->belongsTo(Mobil::class);
    }
}
