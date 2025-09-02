<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterKegiatan extends Model
{
    use HasFactory;

    protected $table = 'master_kegiatans';

    protected $fillable = [
        'kode_kegiatan',
        'nama_kegiatan',
        'keterangan',
        'status',
    ];
}
