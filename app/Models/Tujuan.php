<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


use App\Traits\Auditable;
class Tujuan extends Model
{
    use HasFactory;

    use Auditable;
    protected $fillable = [
        'deskripsi',
        'uang_jalan',
        'cabang',
        'wilayah',
        'dari',
        'ke',
        'uang_jalan_20',
        'ongkos_truk_20',
        'uang_jalan_40',
        'ongkos_truk_40',
        'antar_20',
        'antar_40',
    ];
}
