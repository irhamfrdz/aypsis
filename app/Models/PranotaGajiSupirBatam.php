<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PranotaGajiSupirBatam extends Model
{
    protected $fillable = ['nomor_pranota', 'tanggal_pranota', 'total_gaji', 'keterangan', 'created_by'];

    protected $casts = ['tanggal_pranota' => 'date', 'total_gaji' => 'decimal:2'];

    public function items()
    {
        return $this->hasMany(PranotaGajiSupirBatamItem::class, 'pranota_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
