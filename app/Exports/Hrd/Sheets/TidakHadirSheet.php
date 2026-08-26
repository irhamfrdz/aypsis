<?php

namespace App\Exports\Hrd\Sheets;

use App\Models\Karyawan;
use App\Models\Absensi;
use App\Models\HariLibur;
use App\Models\PersetujuanAbsensi;
use App\Models\Cuti;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TidakHadirSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
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

        $absensiRaw = Absensi::whereBetween('waktu', [
                $this->startDate . ' 00:00:00',
                $this->endDate . ' 23:59:59'
            ])
            ->selectRaw('karyawan_id, DATE(waktu) as tanggal')
            ->groupBy('karyawan_id', 'tanggal')
            ->get()
            ->groupBy('tanggal');

        $hariLiburs = HariLibur::whereBetween('tanggal', [$this->startDate, $this->endDate])->pluck('tanggal')->toArray();

        // Get Permissions
        $izins = PersetujuanAbsensi::where('status', 'approved')
            ->where(function($q) {
                $q->whereBetween('tanggal_mulai', [$this->startDate, $this->endDate])
                  ->orWhereBetween('tanggal_selesai', [$this->startDate, $this->endDate])
                  ->orWhere(function($sub) {
                      $sub->where('tanggal_mulai', '<=', $this->startDate)
                          ->where('tanggal_selesai', '>=', $this->endDate);
                  });
            })
            ->select('karyawan_id', 'tanggal_mulai', 'tanggal_selesai', 'jenis_izin');

        $cutis = Cuti::where('status', 'approved')
            ->where(function($q) {
                $q->whereBetween('tanggal_mulai', [$this->startDate, $this->endDate])
                  ->orWhereBetween('tanggal_selesai', [$this->startDate, $this->endDate])
                  ->orWhere(function($sub) {
                      $sub->where('tanggal_mulai', '<=', $this->startDate)
                          ->where('tanggal_selesai', '>=', $this->endDate);
                  });
            })
            ->select('karyawan_id', 'tanggal_mulai', 'tanggal_selesai', 'jenis_cuti as jenis_izin');

        $permissions = $izins->union($cutis)->get()->groupBy('karyawan_id');

        $data = [];
        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);
        
        $today = Carbon::today();

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $dateString = $date->toDateString();
            
            // Skip future dates
            if ($date->greaterThan($today)) {
                continue;
            }
            
            $isWeekend = $date->isSunday(); // Asumsikan minggu libur
            $isLibur = in_array($dateString, $hariLiburs);

            if ($isWeekend || $isLibur) {
                continue;
            }

            $logsForDay = $absensiRaw->get($dateString, collect())->keyBy('karyawan_id');

            foreach ($karyawans as $karyawan) {
                // If they have attendance log, skip
                if ($logsForDay->has($karyawan->id)) {
                    continue;
                }

                // Check permissions
                $karyawanPermissions = $permissions->get($karyawan->id, collect());
                $matchedPerm = $karyawanPermissions->first(function($perm) use ($dateString) {
                    return $dateString >= $perm->tanggal_mulai && $dateString <= $perm->tanggal_selesai;
                });

                $isFullDayPerm = false;
                $keterangan = 'Alpha (Tanpa Keterangan)';
                
                if ($matchedPerm) {
                    $jenis = strtolower($matchedPerm->jenis_izin);
                    if (!str_contains($jenis, 'datang_terlambat') && !str_contains($jenis, 'pulang_cepat') && !str_contains($jenis, 'dinas_luar')) {
                        $isFullDayPerm = true;
                        $keterangan = ucwords(str_replace('_', ' ', $matchedPerm->jenis_izin));
                    }
                }

                if (!$isFullDayPerm) {
                    $data[] = [
                        'Tanggal' => $date->format('Y-m-d'),
                        'Hari' => $date->translatedFormat('l'),
                        'NIK' => $karyawan->nik,
                        'Nama Karyawan' => $karyawan->nama_lengkap,
                        'Divisi' => $karyawan->divisi,
                        'Keterangan' => $keterangan
                    ];
                }
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
            'Keterangan'
        ];
    }

    public function title(): string
    {
        return 'Tidak Hadir';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EF4444']]],
        ];
    }
}
