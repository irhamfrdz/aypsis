<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


use App\Traits\Auditable;
class Cabang extends Model
{
    use HasFactory;

    use Auditable;
    protected $fillable = [
        'nama_cabang',
        'keterangan'
    ];

    protected $table = 'cabangs';
}
