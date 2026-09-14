<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterTypeBonAmprahan extends Model
{
    use HasFactory;

    protected $table = 'master_type_bon_amprahans';

    protected $fillable = [
        'kode',
        'nama',
        'keterangan',
        'status',
    ];
}
