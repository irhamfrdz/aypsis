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
        'created_by',
        'updated_by',
        'jht_biaya',
        'jht_hutang',
        'jkk_tunjangan',
        'jkm_tunjangan',
        'jp_biaya',
        'jp_hutang',
    ];
}
