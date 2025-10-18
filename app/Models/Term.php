<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


use App\Traits\Auditable;
class Term extends Model
{
    use Auditable;

    protected $fillable = [
        'kode',
        'nama_status',
        'catatan',
        'status'
    ];
}
