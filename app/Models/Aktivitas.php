<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aktivitas extends Model
{
    use HasFactory;

    protected $table = 'aktivitas';

    protected $fillable = [
        'kode',
        'nama_aktivitas',
        'catatan',
        'status'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    // Scope for active records
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Scope for inactive records
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }
}
