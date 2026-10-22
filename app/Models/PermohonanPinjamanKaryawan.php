<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermohonanPinjamanKaryawan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_permohonan',
        'karyawan_id',
        'tanggal_permohonan',
        'tanggal_dibutuhkan',
        'nominal',
        'tenor_bulan',
        'rencana_cicilan',
        'tujuan_pinjaman',
        'catatan_pemohon',
        'status',
        'nominal_disetujui',
        'tenor_disetujui',
        'catatan_approval',
        'created_by',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'saldo_recorded_at',
    ];

    protected $casts = [
        'tanggal_permohonan' => 'date',
        'tanggal_dibutuhkan' => 'date',
        'nominal' => 'decimal:2',
        'rencana_cicilan' => 'decimal:2',
        'nominal_disetujui' => 'decimal:2',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'saldo_recorded_at' => 'datetime',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejector()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }
}
