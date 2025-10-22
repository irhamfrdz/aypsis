<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Karyawan extends Model
{
    use HasFactory, Auditable;

    protected $table = 'karyawans';

    // Keep a broad $fillable to avoid modifying DB structure
    protected $fillable = [
        'user_id', 'nik', 'nama_panggilan', 'nama_lengkap', 'plat', 'email', 'ktp', 'kk',
        'alamat', 'rt_rw', 'kelurahan', 'kecamatan', 'kabupaten', 'provinsi', 'kode_pos',
        'alamat_lengkap', 'tempat_lahir', 'tanggal_lahir', 'no_hp', 'jenis_kelamin',
        'status_perkawinan', 'agama', 'divisi', 'pekerjaan', 'tanggal_masuk', 'tanggal_berhenti',
        'tanggal_masuk_sebelumnya', 'tanggal_berhenti_sebelumnya', 'catatan', 'status_pajak',
        'nama_bank', 'bank_cabang', 'akun_bank', 'atas_nama', 'jkn', 'no_ketenagakerjaan',
        'cabang', 'nik_supervisor', 'supervisor', 'verification_status', 'verified_by', 'verified_at'
    ];

    protected $dates = [
        'tanggal_lahir', 'tanggal_masuk', 'tanggal_berhenti', 'tanggal_masuk_sebelumnya', 'tanggal_berhenti_sebelumnya', 'verified_at'
    ];

    // In this codebase users table has nullable karyawan_id; keep inverse relation here.
    public function user()
    {
        return $this->hasOne(User::class, 'karyawan_id', 'id');
    }

    public function crewChecklists()
    {
        return $this->hasMany(CrewEquipment::class, 'karyawan_id', 'id');
    }

    public function familyMembers()
    {
        return $this->hasMany(KaryawanFamilyMember::class);
    }

    public function isAbk(): bool
    {
        return strtolower((string)($this->divisi ?? '')) === 'abk' || strtolower((string)($this->pekerjaan ?? '')) === 'abk';
    }

    /**
     * Format date attribute to specified format
     *
     * @param string $attribute
     * @param string $format
     * @return string|null
     */
    public function formatAsDate(string $attribute, string $format = 'Y-m-d'): ?string
    {
        $value = $this->getAttribute($attribute);

        if (!$value) {
            return null;
        }

        // If it's already a Carbon instance
        if ($value instanceof \Carbon\Carbon) {
            return $value->format($format);
        }

        // If it's a string, try to parse it
        try {
            return \Carbon\Carbon::parse($value)->format($format);
        } catch (\Exception $e) {
            // If parsing fails, return the original value
            return $value;
        }
    }
}
