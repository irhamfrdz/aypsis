<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratJalanApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'surat_jalan_id',
        'approval_level',
        'status',
        'approved_by',
        'approval_notes',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    /**
     * Relationship dengan SuratJalan
     */
    public function suratJalan()
    {
        return $this->belongsTo(SuratJalan::class);
    }

    /**
     * Relationship dengan User (approver)
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope untuk status pending
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope untuk status approved
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope untuk status rejected
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope untuk approval level tertentu
     */
    public function scopeByLevel($query, $level)
    {
        return $query->where('approval_level', $level);
    }
}