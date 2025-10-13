<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengirim extends Model
{
    protected $fillable = [
        'kode',
        'nama_pengirim',
        'catatan',
        'status'
    ];
}
