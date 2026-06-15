<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterKartuBensinBatamHistory extends Model
{
    use HasFactory;

    protected $table = 'master_kartu_bensin_batam_histories';

    protected $fillable = [
        'master_kartu_bensin_batam_id',
        'tanggal',
        'tipe',
        'nominal',
        'saldo_sebelum',
        'saldo_sesudah',
        'keterangan',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'nominal' => 'decimal:2',
        'saldo_sebelum' => 'decimal:2',
        'saldo_sesudah' => 'decimal:2',
    ];

    // Relationships
    public function card()
    {
        return $this->belongsTo(MasterKartuBensinBatam::class, 'master_kartu_bensin_batam_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
