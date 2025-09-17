<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Divisi extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_divisi',
        'kode_divisi',
        'deskripsi',
        'is_active'
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
            $q->where('nama_divisi', 'LIKE', '%' . $search . '%')
              ->orWhere('kode_divisi', 'LIKE', '%' . $search . '%')
              ->orWhere('deskripsi', 'LIKE', '%' . $search . '%');
        });
    }
}
