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
        'tanggal_permohonan',
        'jenis_amprahan',
        'kapal_id',
        'mobil_id',
        'nomor_voyage',
        'status',
        'keterangan_umum',
    ];

    protected $casts = [
        'tanggal_permohonan' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kapal()
    {
        return $this->belongsTo(MasterKapal::class, 'kapal_id');
    }

    public function mobil()
    {
        return $this->belongsTo(Mobil::class, 'mobil_id');
    }

    public function items()
    {
        return $this->hasMany(PermohonanAmprahanItem::class, 'permohonan_id');
    }
}
