<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AbsensiExport implements FromCollection, WithHeadings, WithMapping
{
    protected $absensis;

    public function __construct($absensis)
    {
        $this->absensis = $absensis;
    }

    public function collection()
    {
        return $this->absensis;
    }

    public function headings(): array
    {
        return [
            'NIK',
            'Nama Karyawan',
            'Pekerjaan',
            'Divisi',
            'Penempatan',
            'Tanggal',
            'Jam Masuk',
            'Jam Pulang',
            'Istirahat Keluar',
            'Istirahat Masuk',
            'Lembur Masuk',
            'Lembur Pulang',
        ];
    }

    public function map($absen): array
    {
        return [
            $absen->nik,
            $absen->karyawan->nama_lengkap ?? '-',
            $absen->karyawan->pekerjaan ?? '-',
            $absen->karyawan->divisi ?? '-',
            $absen->karyawan->penempatan ?? '-',
            \Carbon\Carbon::parse($absen->tanggal)->format('d-m-Y'),
            $absen->waktu_masuk ? \Carbon\Carbon::parse($absen->waktu_masuk)->format('H:i:s') : '-',
            $absen->waktu_pulang ? \Carbon\Carbon::parse($absen->waktu_pulang)->format('H:i:s') : '-',
            $absen->waktu_istirahat_keluar ? \Carbon\Carbon::parse($absen->waktu_istirahat_keluar)->format('H:i:s') : '-',
            $absen->waktu_istirahat_masuk ? \Carbon\Carbon::parse($absen->waktu_istirahat_masuk)->format('H:i:s') : '-',
            $absen->waktu_lembur_masuk ? \Carbon\Carbon::parse($absen->waktu_lembur_masuk)->format('H:i:s') : '-',
            $absen->waktu_lembur_pulang ? \Carbon\Carbon::parse($absen->waktu_lembur_pulang)->format('H:i:s') : '-',
        ];
    }
}
