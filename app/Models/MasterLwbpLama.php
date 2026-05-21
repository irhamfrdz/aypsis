<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterLwbpLama extends Model
{
    use Auditable, HasFactory;

    protected $table = 'master_lwbp_lamas';

    protected $fillable = [
        'tahun',
        'bulan',
        'biaya',
        'status',
    ];
}
