<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterTunjangan extends Model
{
    use Auditable, HasFactory;

    protected $table = 'master_tunjangans';

    protected $fillable = [
        'nama_tunjangan',
        'keterangan',
        'status',
    ];
}
