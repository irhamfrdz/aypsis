<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaKapalTemasStage extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'nilai_tagihan' => 'decimal:2',
        'dp_diperhitungkan' => 'decimal:2',
        'nominal_dibayar' => 'decimal:2',
    ];

    public function biayaKapal()
    {
        return $this->belongsTo(BiayaKapal::class);
    }

    public function dpStage()
    {
        return $this->belongsTo(self::class, 'dp_stage_id');
    }

    public function settlements()
    {
        return $this->hasMany(self::class, 'dp_stage_id');
    }

    public function details()
    {
        return $this->hasMany(BiayaKapalTemas::class, 'temas_stage_id');
    }
}
