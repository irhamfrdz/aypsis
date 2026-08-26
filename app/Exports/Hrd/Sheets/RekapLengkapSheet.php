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

class RekapLengkapSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
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
        $karyawans = Karyawan::where('status', 'active')->orderBy('nama_lengkap')->get();

        $startDateTime = Carbon::parse($this->startDate)->setTime(6, 0, 0);
        $endDateTime = Carbon::parse($this->endDate)->addDay()->setTime(5, 59, 59);

        $absensiRaw = Absensi::whereBetween('waktu', [$startDateTime, $endDateTime])
            ->selectRaw('karyawan_id, DATE(DATE_SUB(waktu, INTERVAL 6 HOUR)) as tanggal, MIN(CASE WHEN LOWER(tipe) IN ("masuk", "check in") THEN waktu END) as waktu_masuk, MAX(CASE WHEN LOWER(tipe) IN ("pulang", "keluar", "check out", "selesai") THEN waktu END) as waktu_pulang')
            ->groupBy('karyawan_id', \Illuminate\Support\Facades\DB::raw('DATE(DATE_SUB(waktu, INTERVAL 6 HOUR))'))
            ->get()
            ->groupBy('tanggal'); // Group by date for easier processing

        $data = [];
        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);

        $hariLiburs = HariLibur::whereBetween('tanggal', [$this->startDate, $this->endDate])->pluck('tanggal')->toArray();

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $dateString = $date->toDateString();
            $isWeekend = $date->isSunday();
            $isLibur = in_array($dateString, $hariLiburs);

            $logsForDay = $absensiRaw->get($dateString, collect())->keyBy('karyawan_id');

            foreach ($karyawans as $karyawan) {
                $log = $logsForDay->get($karyawan->id);
                
                if ($log) {
                    $waktuMasuk = $log->waktu_masuk ? Carbon::parse($log->waktu_masuk)->format('H:i') : '-';
                    $waktuPulang = $log->waktu_pulang ? Carbon::parse($log->waktu_pulang)->format('H:i') : '-';
                    
                    $status = 'Hadir';
                } else {
                    $waktuMasuk = '-';
                    $waktuPulang = '-';
                    $status = ($isWeekend || $isLibur) ? 'Libur' : 'Tidak Hadir';
                }

                $data[] = [
                    'Tanggal' => $date->format('Y-m-d'),
                    'Hari' => $date->translatedFormat('l'),
                    'NIK' => $karyawan->nik,
                    'Nama Karyawan' => $karyawan->nama_lengkap,
                    'Divisi' => $karyawan->divisi,
                    'Jam Masuk' => $waktuMasuk,
                    'Jam Pulang' => $waktuPulang,
                    'Status' => $status
                ];
            }
        }

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
            'Jam Pulang',
            'Status'
        ];
    }

    public function title(): string
    {
        return 'Rekap Lengkap Absen';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']]],
        ];
    }
}
