<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MasterLwbpLama extends Model
{
    use HasFactory, Auditable;

    protected $table = 'master_lwbp_lamas';

    protected $fillable = [
        'tahun',
        'bulan',
        'biaya',
        'status',
    ];
}
