<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterKegiatan extends Model
{
    use Auditable;
    use Auditable, HasFactory;

    protected $table = 'master_kegiatans';

    protected $fillable = [
        'kode_kegiatan',
        'nama_kegiatan',
        'type',
        'keterangan',
        'status',
    ];
}
