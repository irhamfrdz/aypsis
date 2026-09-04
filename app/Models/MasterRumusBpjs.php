<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterRumusBpjs extends Model
{
    use HasFactory, Auditable, SoftDeletes;

    protected $table = 'master_rumus_bpjs';

    protected $fillable = [
        'jenis',
        'group_name',
        'tipe_rumus',
        'nilai',
        'tunjangan_persen',
        'hutang_persen',
        'biaya_persen',
        'keterangan_custom',
    ];
}
