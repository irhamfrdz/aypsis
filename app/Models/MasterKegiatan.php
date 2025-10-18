<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class MasterKegiatan extends Model
{
    use HasFactory, Auditable;

    use Auditable;
    protected $table = 'master_kegiatans';

    protected $fillable = [
        'kode_kegiatan',
        'nama_kegiatan',
        'type',
        'keterangan',
        'status',
    ];
}
