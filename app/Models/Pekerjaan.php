<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pekerjaan extends Model
{
    use Auditable;
    use HasFactory;

    protected $fillable = [
        'nama_pekerjaan',
        'kode_pekerjaan',
        'deskripsi',
        'divisi',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relasi dengan Karyawan - menggunakan LIKE untuk matching yang lebih fleksibel
    public function karyawans()
    {
        return $this->hasMany(Karyawan::class, 'pekerjaan', 'nama_pekerjaan')
            ->orWhere('pekerjaan', 'LIKE', '%'.$this->nama_pekerjaan.'%');
    }

    // Scope untuk pekerjaan aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope untuk pencarian
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nama_pekerjaan', 'LIKE', '%'.$search.'%')
                ->orWhere('kode_pekerjaan', 'LIKE', '%'.$search.'%')
                ->orWhere('deskripsi', 'LIKE', '%'.$search.'%');
        });
    }
}
