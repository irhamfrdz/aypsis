<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Karyawan extends Model
{
    use HasFactory;

    // Menentukan nama tabel yang digunakan oleh model ini
    protected $table = 'karyawans';

    // Daftar Kolom yang bisa diisi secara massal
    protected $fillable = [
        'nik',
        'nama_panggilan',
        'nama_lengkap',
        'plat',
        'email',
        'ktp',
        'kk',
        'alamat',
        'rt_rw',
        'kelurahan',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'kode_pos',
        'alamat_lengkap',
        'tempat_lahir',
        'tanggal_lahir',
        'no_hp',
        'jenis_kelamin',
        'status_perkawinan',
        'agama',
        'divisi',
        'pekerjaan',
        'tanggal_masuk',
        'tanggal_berhenti',
        'tanggal_masuk_sebelumnya',
        'tanggal_berhenti_sebelumnya',
        'catatan',
        'status_pajak',
        'nama_bank',
        'bank_cabang',
        'akun_bank',
        'atas_nama',
        'jkn',
        'no_ketenagakerjaan',
        'cabang',
        'nik_supervisor',
        'supervisor'
    ];

    // Menentukan kolom tanggal yang akan diparsing sebagai instance Carbon
    protected $dates = [
        'tanggal_lahir',
        'tanggal_masuk',
        'tanggal_berhenti',
        'tanggal_masuk_sebelumnya',
        'tanggal_berhenti_sebelumnya',
    ];

    /**
     * Utility: Format a date-like attribute from the model.
     * Returns null when value is empty/unparseable.
     */
    public function formatAsDate(string $attribute, string $format = 'd/m/Y')
    {
        $value = $this->$attribute ?? null;
        if (empty($value)) return null;
        if ($value instanceof Carbon || $value instanceof \DateTimeInterface) {
            try { return $value->format($format); } catch (\Throwable $e) { return null; }
        }
        // Try to parse string
        $ts = strtotime((string)$value);
        if ($ts === false || $ts === -1) return null;
        return date($format, $ts);
    }

    /**
     * Human readable label for jenis_kelamin.
     */
    public function getJenisKelaminLabelAttribute()
    {
        $map = ['L' => 'Laki-laki', 'P' => 'Perempuan'];
        if (empty($this->jenis_kelamin)) return null;
        return $map[$this->jenis_kelamin] ?? $this->jenis_kelamin;
    }

    /**
     * Human readable label for status_pajak (best-effort).
     */
    public function getStatusPajakLabelAttribute()
    {
        if (empty($this->status_pajak)) return null;
        $v = strtoupper((string)$this->status_pajak);
        if (strpos($v, 'TK') !== false) return 'Tidak Kawin';
        if (strpos($v, 'K') !== false) return 'Kawin';
        return $this->status_pajak;
    }

    /**
     * Relasi ke user account
     */
    public function user()
    {
        return $this->hasOne(User::class, 'karyawan_id', 'id');
    }

    /**
     * Relasi ke crew checklist
     */
    public function crewChecklists()
    {
        return $this->hasMany(CrewEquipment::class);
    }

    /**
     * Check if karyawan is ABK division
     */
    public function isAbk()
    {
        return strtolower($this->divisi) === 'abk';
    }

}
