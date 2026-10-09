<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PranotaObMuatTemas extends Model
{
    protected $table = 'pranota_ob_muat_temas';

    protected $fillable = [
        'nomor_pranota', 'tanggal_pranota', 'nama_kapal', 'no_voyage',
        'nomor_accurate', 'nominal', 'adjustment', 'grand_total', 'keterangan', 'created_by',
    ];

    protected $casts = [
        'tanggal_pranota' => 'date',
        'nominal' => 'decimal:2',
        'adjustment' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(PranotaObMuatTemasItem::class, 'pranota_ob_muat_temas_id');
    }
}
