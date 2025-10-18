<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


use App\Traits\Auditable;
class TipeAkun extends Model
{
    use Auditable;

    protected $table = 'tipe_akuns';

    protected $fillable = [
        'tipe_akun',
        'catatan'
    ];
}
