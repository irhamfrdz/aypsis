<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GateInAktivitas extends Model
{
    use HasFactory;

    protected $table = 'gate_in_aktivitas_details';

    protected $fillable = [
        'gate_in_id',
        'aktivitas',
        's_t_s',
        'box',
        'itm',
        'tarif',
        'total'
    ];

    protected $casts = [
        'tarif' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    /**
     * Relationship to GateIn
     */
    public function gateIn()
    {
        return $this->belongsTo(GateIn::class);
    }
}
