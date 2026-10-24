<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaKapalMeratusStage extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_dp' => 'date',
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
        return $this->belongsToMany(self::class, 'meratus_dp_references', 'dp_stage_id', 'settlement_stage_id')->withPivot('nominal_digunakan');
    }

    public function dpStages()
    {
        return $this->belongsToMany(self::class, 'meratus_dp_references', 'settlement_stage_id', 'dp_stage_id')->withPivot('nominal_digunakan');
    }

    public function details()
    {
        return $this->hasMany(BiayaKapalMeratus::class, 'meratus_stage_id');
    }
}
