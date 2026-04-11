<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class PranotaOngkosTruk extends Model
{
    use HasFactory, Auditable;

    protected $table = 'pranota_ongkos_truks';

    protected $fillable = [
        'no_pranota',
        'tanggal_pranota',
        'total_nominal',
        'keterangan',
        'status',
        'created_by',
    ];

    protected $casts = [
        'tanggal_pranota' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(PranotaOngkosTrukItem::class, 'pranota_ongkos_truk_id');
    }
}
