<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KaryawanFamilyMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'karyawan_id',
        'hubungan',
        'nama',
        'tanggal_lahir',
        'alamat',
        'no_telepon',
        'nik_ktp',
        'no_bpjs_kesehatan',
        'faskes'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}
