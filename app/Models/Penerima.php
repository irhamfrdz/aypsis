<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Penerima extends Model
{
    use Auditable;

    protected $fillable = [
        'kode',
        'nama_penerima',
        'catatan',
        'status'
    ];
}
