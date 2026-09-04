<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PranotaBpjsDetail extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'bpjs_kesehatan' => 'decimal:2',
        'bpjs_ketenagakerjaan' => 'decimal:2',
        'jht_biaya' => 'decimal:2',
        'jht_hutang' => 'decimal:2',
        'jkk_tunjangan' => 'decimal:2',
        'jkm_tunjangan' => 'decimal:2',
        'jp_biaya' => 'decimal:2',
        'jp_hutang' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function header()
    {
        return $this->belongsTo(PranotaBpjsHeader::class, 'pranota_bpjs_header_id');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }
}
