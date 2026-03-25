<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TtLclBatamKontainerPivot extends Model
{
    use HasFactory;

    protected $table = 'tt_lcl_batam_kontainer_pivot';

    protected $fillable = [
        'tt_lcl_batam_id',
        'kontainer_id',
        'nomor_kontainer',
        'nomor_seal',
        'tanggal_seal',
        'is_split',
        'split_from_nomor',
        'split_volume',
        'split_tonase',
        'split_keterangan',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'is_split' => 'boolean',
        'tanggal_seal' => 'date',
    ];

    public function tandaTerimaLclBatam()
    {
        return $this->belongsTo(TandaTerimaLclBatam::class, 'tt_lcl_batam_id');
    }

    public function kontainer()
    {
        return $this->belongsTo(Kontainer::class);
    }
}
