<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bl extends Model
{
    use HasFactory;

    protected $table = 'bls';

    protected $fillable = [
        'prospek_id',
        'nomor_bl',
        'nomor_kontainer',
        'no_seal',
        'tipe_kontainer',
        'no_voyage',
        'nama_kapal',
        'nama_barang',
        'tonnage',
        'volume',
        'term',
        'kuantitas',
        'supir_ob',
        'status_bongkar',
    ];

    /**
     * Get the prospek that owns the BL.
     */
    public function prospek()
    {
        return $this->belongsTo(Prospek::class);
    }
}
