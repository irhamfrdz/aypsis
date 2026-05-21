<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    use Auditable;

    protected $fillable = [
        'kode',
        'nama_status',
        'catatan',
        'status',
    ];
}
