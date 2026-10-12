<?php

namespace App\Exports\Hrd\Sheets;

use App\Models\Absensi;
use App\Models\Karyawan;
use App\Services\WorkingHoursService;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TerlambatSheet implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles, WithTitle
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

        $startDateTime = Carbon::parse($this->startDate)->setTime(6, 0, 0);
        $endDateTime = Carbon::parse($this->endDate)->addDay()->setTime(5, 59, 59);

        $absensiRaw = Absensi::whereBetween('waktu', [$startDateTime, $endDateTime])
            ->selectRaw('karyawan_id, DATE(DATE_SUB(waktu, INTERVAL 6 HOUR)) as tanggal, MIN(CASE WHEN LOWER(tipe) IN ("masuk", "check in") THEN waktu END) as waktu_masuk')
            ->groupBy('karyawan_id', \Illuminate\Support\Facades\DB::raw('DATE(DATE_SUB(waktu, INTERVAL 6 HOUR))'))
            ->get();

        $data = [];
        $workingHours = new WorkingHoursService;

        foreach ($absensiRaw as $log) {
            if (! $log->waktu_masuk || ! isset($karyawans[$log->karyawan_id])) {
                continue;
            }

            $waktuMasuk = Carbon::parse($log->waktu_masuk);
            $tanggal = Carbon::parse($log->tanggal);
            $karyawan = $karyawans[$log->karyawan_id];
            $menitTerlambat = $workingHours->lateMinutes($karyawan, $tanggal, $waktuMasuk);

            if ($menitTerlambat > 0 && ! $karyawan->isExemptFromTerlambat()) {

                $data[] = [
                    'Tanggal' => $tanggal->format('Y-m-d'),
                    'Hari' => $tanggal->translatedFormat('l'),
                    'NIK' => $karyawan->nik,
                    'Nama Karyawan' => $karyawan->nama_lengkap,
                    'Divisi' => $karyawan->divisi,
                    'Jam Masuk' => $waktuMasuk->format('H:i'),
                    'Menit Terlambat' => $menitTerlambat.' Menit',
                ];
            }
        }

        // Sort by tanggal then nama
        usort($data, function ($a, $b) {
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
            'Menit Terlambat',
        ];
    }

    public function title(): string
    {
        return 'Terlambat';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EF4444']]],
        ];
    }
}
