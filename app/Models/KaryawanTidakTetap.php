<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KaryawanTidakTetap extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'nik',
        'nama_lengkap',
        'nama_panggilan',
        'divisi',
        'pekerjaan',
        'cabang',
        'nik_ktp',
        'jenis_kelamin',
        'agama',
        'rt_rw',
        'alamat_lengkap',
        'kelurahan',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'kode_pos',
        'email',
        'tanggal_masuk',
        'status_pajak',
        'penempatan',
        'group',
        'sub_group',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'group' => 'array',
        'sub_group' => 'array',
    ];

    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'nik', 'nik');
    }

    public function uangMakans()
    {
        return $this->morphMany(UangMakan::class, 'karyawan');
    }

    public function uangMakanTerbaru()
    {
        return $this->morphOne(UangMakan::class, 'karyawan')->latestOfMany('tanggal');
    }

    public function payrollUangMakan()
    {
        return $this->morphMany(PayrollUangMakan::class, 'karyawan');
    }
}
