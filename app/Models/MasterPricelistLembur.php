<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterPricelistLembur extends Model
{
    protected $fillable = [
        'nama',
        'nominal',
        'keterangan',
        'status',
    ];
}
