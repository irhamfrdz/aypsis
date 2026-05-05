<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buruh extends Model
{
    protected $fillable = [
        'nama',
        'telepon',
        'alamat',
        'jabatan',
        'status'
    ];
}
