<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermohonanAmprahan extends Model
{
    use HasFactory;

    protected $table = 'permohonan_amprahans';

    protected $fillable = [
        'user_id',
        'kapal_id',
        'nomor_voyage',
        'status',
        'keterangan_umum',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kapal()
    {
        return $this->belongsTo(MasterKapal::class, 'kapal_id');
    }

    public function items()
    {
        return $this->hasMany(PermohonanAmprahanItem::class, 'permohonan_id');
    }
}
