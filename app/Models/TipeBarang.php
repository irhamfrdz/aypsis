<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class TipeBarang extends Model
{
    use SoftDeletes;
    use Auditable;

    protected $fillable = [
        'kode',
        'nama',
        'keterangan',
    ];
}
