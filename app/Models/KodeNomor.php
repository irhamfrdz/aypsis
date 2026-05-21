<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KodeNomor extends Model
{
    use Auditable;
    use HasFactory;

    protected $table = 'kode_nomor';

    protected $fillable = [
        'kode',
        'nama',
        'catatan',
    ];
}
