<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class TipeAkun extends Model
{
    use Auditable;

    protected $table = 'tipe_akuns';

    protected $fillable = [
        'tipe_akun',
        'catatan',
    ];
}
