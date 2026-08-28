<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PranotaBpjsHeader extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_pranota' => 'date',
        'approved_at' => 'datetime',
        'total_bpjs_kesehatan' => 'decimal:2',
        'total_bpjs_ketenagakerjaan' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function details()
    {
        return $this->hasMany(PranotaBpjsDetail::class, 'pranota_bpjs_header_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
