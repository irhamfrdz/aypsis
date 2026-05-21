<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cabang extends Model
{
    use Auditable;
    use HasFactory;

    protected $fillable = [
        'nama_cabang',
        'keterangan',
    ];

    protected $table = 'cabangs';
}
