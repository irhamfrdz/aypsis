<?php

namespace App\Exports\Hrd\Sheets;

use App\Models\Karyawan;
use App\Models\Absensi;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PulangCepatSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
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
            ->selectRaw('karyawan_id, DATE(waktu) as tanggal, MAX(CASE WHEN tipe IN ("Pulang", "Keluar", "Check Out", "Selesai") THEN waktu END) as waktu_pulang')
            ->groupBy('karyawan_id', 'tanggal')
            ->get();

        $data = [];

        foreach ($absensiRaw as $log) {
            if (!$log->waktu_pulang || !isset($karyawans[$log->karyawan_id])) {
                continue;
            }

            $waktuPulang = Carbon::parse($log->waktu_pulang);
            $tanggal = $waktuPulang->copy()->startOfDay();
            $isSaturday = $tanggal->isSaturday();

            // Batas pulang cepat: 17:00 (Senin-Jumat) atau 14:00 (Sabtu)
            $jamBatasPulang = $isSaturday ? 14 : 17;
            $batasPulang = $tanggal->copy()->setHour($jamBatasPulang)->setMinute(0)->setSecond(0);

            if ($waktuPulang->lessThan($batasPulang)) {
                $menitCepat = $waktuPulang->diffInMinutes($batasPulang);
                $karyawan = $karyawans[$log->karyawan_id];

                $data[] = [
                    'Tanggal' => $tanggal->format('Y-m-d'),
                    'Hari' => $tanggal->translatedFormat('l'),
                    'NIK' => $karyawan->nik,
                    'Nama Karyawan' => $karyawan->nama_lengkap,
                    'Divisi' => $karyawan->divisi,
                    'Jam Pulang' => $waktuPulang->format('H:i'),
                    'Pulang Cepat' => $menitCepat . ' Menit'
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
            'Jam Pulang',
            'Pulang Cepat'
        ];
    }

    public function title(): string
    {
        return 'Pulang Cepat';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F59E0B']]],
        ];
    }
}
