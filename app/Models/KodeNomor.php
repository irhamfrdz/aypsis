<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


use App\Traits\Auditable;
class KodeNomor extends Model
{
    use HasFactory;

    use Auditable;
    protected $table = 'kode_nomor';

    protected $fillable = [
        'kode',
        'nama',
        'catatan',
    ];
}
