<?php

namespace App\Exports\Hrd\Sheets;

use App\Models\Karyawan;
use App\Models\Absensi;
use App\Models\HariLibur;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TerlambatSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $karyawans = Karyawan::where('status', 'active')->get()->keyBy('id');

        $absensiRaw = Absensi::whereBetween('waktu', [
                $this->startDate . ' 00:00:00',
                $this->endDate . ' 23:59:59'
            ])
            ->selectRaw('karyawan_id, DATE(waktu) as tanggal, MIN(CASE WHEN tipe IN ("Masuk", "Check In") THEN waktu END) as waktu_masuk')
            ->groupBy('karyawan_id', 'tanggal')
            ->get();

        $data = [];

        foreach ($absensiRaw as $log) {
            if (!$log->waktu_masuk || !isset($karyawans[$log->karyawan_id])) {
                continue;
            }

            $waktuMasuk = Carbon::parse($log->waktu_masuk);
            $tanggal = $waktuMasuk->copy()->startOfDay();
            $isSaturday = $tanggal->isSaturday();

            // Batas terlambat: 09:05 (Senin-Jumat) atau 08:05 (Sabtu)
            $jamBatas = $isSaturday ? 8 : 9;
            $batasTerlambat = $tanggal->copy()->setHour($jamBatas)->setMinute(5)->setSecond(0);

            if ($waktuMasuk->greaterThan($batasTerlambat)) {
                $menitTerlambat = $batasTerlambat->copy()->setMinute(0)->diffInMinutes($waktuMasuk);
                $karyawan = $karyawans[$log->karyawan_id];

                $data[] = [
                    'Tanggal' => $tanggal->format('Y-m-d'),
                    'Hari' => $tanggal->translatedFormat('l'),
                    'NIK' => $karyawan->nik,
                    'Nama Karyawan' => $karyawan->nama_lengkap,
                    'Divisi' => $karyawan->divisi,
                    'Jam Masuk' => $waktuMasuk->format('H:i'),
                    'Menit Terlambat' => $menitTerlambat . ' Menit'
                ];
            }
        }

        // Sort by tanggal then nama
        usort($data, function($a, $b) {
            if ($a['Tanggal'] == $b['Tanggal']) {
                return $a['Nama Karyawan'] <=> $b['Nama Karyawan'];
            }
            return $a['Tanggal'] <=> $b['Tanggal'];
        });

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Hari',
            'NIK',
            'Nama Karyawan',
            'Divisi',
            'Jam Masuk',
            'Menit Terlambat'
        ];
    }

    public function title(): string
    {
        return 'Terlambat';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EF4444']]],
        ];
    }
}
