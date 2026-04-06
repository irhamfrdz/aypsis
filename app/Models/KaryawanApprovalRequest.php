<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KaryawanApprovalRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'karyawan_id',
        'user_id',
        'data_before',
        'data_after',
        'status',
        'approved_by',
        'approved_at',
        'reason'
    ];

    protected $casts = [
        'data_before' => 'array',
        'data_after' => 'array',
        'approved_at' => 'datetime'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
