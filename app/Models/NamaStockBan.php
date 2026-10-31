<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NamaStockBan extends Model
{
    use Auditable;
    use SoftDeletes;

    protected $fillable = [
        'nama',
        'type',
        'kuantitas',
        'status',
    ];

    protected $casts = [
        'kuantitas' => 'integer',
    ];

    public function usesMasterStockDefaults(): bool
    {
        $name = strtoupper(preg_replace('/\s+/u', ' ', trim($this->nama ?? '')));

        return in_array($name, [
            'POMPA AIR ALKON BESAR',
            'POMPA AIR ALKON KECIL',
            'PULLEY CRANE',
        ], true);
    }
}
