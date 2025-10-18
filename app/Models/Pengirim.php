<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Pengirim extends Model
{
    
    use Auditable;

protected $fillable = [
        'kode',
        'nama_pengirim',
        'catatan',
        'status'
    ];
}
