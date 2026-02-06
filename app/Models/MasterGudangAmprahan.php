<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MasterGudangAmprahan extends Model
{
    use HasFactory;

    protected $table = 'master_gudang_amprahans';

    protected $fillable = [
        'nama_gudang',
        'lokasi',
        'keterangan',
        'status',
    ];
}
