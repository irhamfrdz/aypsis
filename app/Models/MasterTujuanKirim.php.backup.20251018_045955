<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterTujuanKirim extends Model
{
    protected $table = 'master_tujuan_kirim';

    protected $fillable = [
        'kode',
        'nama_tujuan',
        'catatan',
        'status'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    // Scope untuk status active
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Scope untuk status inactive
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    // Method untuk mendapatkan status badge
    public function getStatusBadgeAttribute()
    {
        return $this->status === 'active' ? 'Aktif' : 'Tidak Aktif';
    }

    // Method untuk mendapatkan status color
    public function getStatusColorAttribute()
    {
        return $this->status === 'active' ? 'success' : 'danger';
    }
}
