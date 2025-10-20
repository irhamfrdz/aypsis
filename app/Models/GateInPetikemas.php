<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GateInPetikemas extends Model
{
    use HasFactory;

    protected $table = 'gate_in_petikemas_details';

    protected $fillable = [
        'gate_in_id',
        'no_petikemas',
        's_t_s',
        'estimasi',
        'estimasi_biaya'
    ];

    protected $casts = [
        'estimasi' => 'date',
        'estimasi_biaya' => 'decimal:2'
    ];

    /**
     * Relationship to GateIn
     */
    public function gateIn()
    {
        return $this->belongsTo(GateIn::class);
    }
}
