<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AbsensiExport implements \Maatwebsite\Excel\Concerns\WithStyles, FromCollection, WithHeadings, WithMapping
{
    protected $absensis;

    protected $filters;

    public function __construct($absensis, $filters = [])
    {
        $this->absensis = $absensis;
        $this->filters = $filters;
    }

    public function collection()
    {
        return $this->absensis;
    }

    public function headings(): array
    {
        return [
            ['LAPORAN DATA ABSENSI KARYAWAN'],
            ['Periode', ': '.($this->filters['start_date'] ?? '-').' s/d '.($this->filters['end_date'] ?? '-')],
            ['Pencarian', ': '.(! empty($this->filters['search']) ? $this->filters['search'] : '-')],
            ['Pekerjaan', ': '.(! empty($this->filters['pekerjaan']) ? $this->filters['pekerjaan'] : '-')],
            ['Departemen', ': '.(! empty($this->filters['departemen']) ? strtoupper($this->filters['departemen']) : '-')],
            ['Cabang', ': '.(! empty($this->filters['cabang']) ? strtoupper($this->filters['cabang']) : '-')],
            ['Divisi', ': '.(! empty($this->filters['divisi']) ? strtoupper($this->filters['divisi']) : '-')],
            [],
            [
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
            ],
        ];
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
            9 => ['font' => ['bold' => true]],
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
