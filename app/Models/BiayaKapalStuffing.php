<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaKapalStuffing extends Model
{
    protected $table = 'biaya_kapal_stuffing';

    protected $fillable = [
        'biaya_kapal_id',
        'kapal',
        'voyage',
        'tanda_terima_ids',
    ];

    protected $casts = [
        'tanda_terima_ids' => 'array',
    ];

    /**
     * Relationship to BiayaKapal
     */
    public function biayaKapal()
    {
        return $this->belongsTo(BiayaKapal::class, 'biaya_kapal_id');
    }

    /**
     * Accessor to get Tanda Terima models
     */
    public function getTandaTerimasAttribute()
    {
        if (empty($this->tanda_terima_ids)) return collect();
        return TandaTerima::whereIn('id', $this->tanda_terima_ids)->get();
    }

    /**
     * Helper to get Tanda Terima models
     */
    public function getTandaTerimas()
    {
        if (empty($this->tanda_terima_ids)) return collect();
        return TandaTerima::whereIn('id', $this->tanda_terima_ids)->get();
    }
}
