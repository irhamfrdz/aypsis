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
        'kuantitas',
        'status',
    ];

    protected $casts = [
        'kuantitas' => 'integer',
    ];
}
