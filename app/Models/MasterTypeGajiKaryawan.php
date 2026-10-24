<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class MasterTypeGajiKaryawan extends Model
{
    use Auditable;

    protected $table = 'type_gaji_karyawan';

    protected $fillable = ['type_gaji_id', 'nama', 'keterangan', 'status'];

    protected $casts = ['type_gaji_id' => 'integer'];
}
