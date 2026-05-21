<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    use Auditable;
    use Auditable, HasFactory;

    protected $fillable = [
        'nama_divisi',
        'kode_divisi',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relasi dengan Karyawan
    public function karyawans()
    {
        return $this->hasMany(Karyawan::class, 'divisi', 'nama_divisi');
    }

    // Scope untuk divisi aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope untuk pencarian
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nama_divisi', 'LIKE', '%'.$search.'%')
                ->orWhere('kode_divisi', 'LIKE', '%'.$search.'%')
                ->orWhere('deskripsi', 'LIKE', '%'.$search.'%');
        });
    }
}
