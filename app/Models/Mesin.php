<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mesin extends Model
{
    use Auditable;
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mesins';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'kode_mesin',
        'nama_mesin',
        'tipe_mesin',
        'ip_address',
        'port',
        'comm_key',
        'status',
        'keterangan',
    ];
}
