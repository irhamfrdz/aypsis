<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaKapalTemasStage extends Model
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
        return $this->belongsToMany(self::class, 'temas_dp_references', 'dp_stage_id', 'settlement_stage_id')->withPivot('nominal_digunakan');
    }

    public function dpStages()
    {
        return $this->belongsToMany(self::class, 'temas_dp_references', 'settlement_stage_id', 'dp_stage_id')->withPivot('nominal_digunakan');
    }

    public function crossDpStages()
    {
        return $this->belongsToMany(BiayaKapalMeratusStage::class, 'meratus_dp_temas_settlements', 'settlement_stage_id', 'dp_stage_id')->withPivot('nominal_digunakan');
    }

    public function crossSettlements()
    {
        return $this->belongsToMany(BiayaKapalMeratusStage::class, 'temas_dp_meratus_settlements', 'dp_stage_id', 'settlement_stage_id')->withPivot('nominal_digunakan');
    }

    public function getSharedDpStagesAttribute()
    {
        return $this->dpStages->concat($this->crossDpStages);
    }

    public function details()
    {
        return $this->hasMany(BiayaKapalTemas::class, 'temas_stage_id');
    }
}
