<?php

namespace App\Exports;

use App\Models\Absensi;
use App\Models\Karyawan;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class AbsensiRekapExport implements FromView, ShouldAutoSize, WithEvents
{
    protected $startDate;
    protected $endDate;
    protected $search;
    protected $pekerjaan;
    protected $divisi;

    public function __construct($startDate, $endDate, $search = null, $pekerjaan = null, $divisi = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->search = $search;
        $this->pekerjaan = $pekerjaan;
        $this->divisi = $divisi;
    }

    public function view(): View
    {
        // Prevent execution timeout
        set_time_limit(900);

        $startDate = Carbon::parse($this->startDate)->startOfDay();
        $endDate = Carbon::parse($this->endDate)->endOfDay();
        
        $totalDays = $startDate->diffInDays($endDate) + 1;

        // Get all employees
        $karyawansQuery = Karyawan::query();
        if (!empty($this->search)) {
            $search = $this->search;
            $karyawansQuery->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }
        if (!empty($this->pekerjaan)) {
            $karyawansQuery->where('pekerjaan', $this->pekerjaan);
        }
        if (!empty($this->divisi)) {
            $karyawansQuery->where('divisi', $this->divisi);
        }
        $karyawans = $karyawansQuery->orderBy('nama_lengkap')->get();

        // Fetch all attendance logs for the date range efficiently using SQL grouping
        $allLogs = Absensi::whereBetween('waktu', [
                $startDate->copy()->setTime(6, 0, 0),
                $endDate->copy()->addDays(1)->setTime(5, 59, 59)
            ])
            ->selectRaw('
                karyawan_id,
                DATE(DATE_SUB(waktu, INTERVAL 6 HOUR)) as tanggal,
                MIN(CASE WHEN LOWER(tipe) = "masuk" THEN waktu ELSE NULL END) as waktu_masuk,
                MAX(CASE WHEN LOWER(tipe) IN ("pulang", "keluar") THEN waktu ELSE NULL END) as waktu_pulang
            ')
            ->groupBy('karyawan_id', \Illuminate\Support\Facades\DB::raw('DATE(DATE_SUB(waktu, INTERVAL 6 HOUR))'))
            ->get()
            ->groupBy('karyawan_id');

        $rekapData = [];
        
        $dayMap = [
            'Sunday' => 'Min',
            'Monday' => 'Sen',
            'Tuesday' => 'Sel',
            'Wednesday' => 'Rab',
            'Thursday' => 'Kam',
            'Friday' => 'Jum',
            'Saturday' => 'Sab',
        ];
        
        $daysData = [];
        $normalDays = 0;
        $dayHeaders = [];
        
        for ($i = 0; $i < $totalDays; $i++) {
            $date = $startDate->copy()->addDays($i);
            $dateString = $date->toDateString();
            $isWeekend = $date->isWeekend();
            if (!$isWeekend) {
                $normalDays++;
            }
            $daysData[$dateString] = [
                'isWeekend' => $isWeekend,
            ];
            $dayHeaders[$dateString] = [
                'date' => $date->format('d/m'),
                'dayName' => $dayMap[$date->format('l')]
            ];
        }

        // Performance Optimization: Cache base timestamps to avoid string conversion overhead
        $limitInTime = strtotime('08:00:00');
        $limitOutTime = strtotime('17:00:00');

        foreach ($karyawans as $karyawan) {
            $logs = $allLogs->get($karyawan->id, collect());
            // Since SQL grouped it by tanggal, we can simply key by tanggal
            $logsByDay = $logs->keyBy('tanggal');

            $dailyStatus = [];
            $riilDays = 0;
            $totalLateMinutes = 0;
            $totalEarlyMinutes = 0;

            foreach ($daysData as $dateString => $dayInfo) {
                $isWeekend = $dayInfo['isWeekend'];
                $log = $logsByDay->get($dateString);

                if (!$log || (!$log->waktu_masuk && !$log->waktu_pulang)) {
                    if ($isWeekend) {
                        $dailyStatus[$dateString] = '';
                    } else {
                        $dailyStatus[$dateString] = 'A';
                    }
                } else {
                    $riilDays++;
                    
                    // Directly extract 'H:i' from 'Y-m-d H:i:s' string avoiding Carbon instantiation
                    $inTimeStr = $log->waktu_masuk ? substr($log->waktu_masuk, 11, 5) : '-';
                    $outTimeStr = $log->waktu_pulang ? substr($log->waktu_pulang, 11, 5) : '-';

                    // Calculate lateness
                    if ($inTimeStr !== '-' && $inTimeStr > '08:00') {
                        $diff = strtotime($inTimeStr.':00') - $limitInTime;
                        if ($diff > 0) {
                            $totalLateMinutes += round($diff / 60);
                        }
                    }

                    // Calculate early departure
                    if ($outTimeStr !== '-' && $outTimeStr < '17:00') {
                        $diff = $limitOutTime - strtotime($outTimeStr.':00');
                        if ($diff > 0) {
                            $totalEarlyMinutes += round($diff / 60);
                        }
                    }

                    $dailyStatus[$dateString] = $inTimeStr . ' - ' . $outTimeStr;
                }
            }

            $absenDays = max(0, $normalDays - $riilDays);

            $rekapData[] = [
                'karyawan' => $karyawan,
                'dailyStatus' => $dailyStatus,
                'normalDays' => $normalDays,
                'riilDays' => $riilDays,
                'absenDays' => $absenDays,
                'lateMinutes' => $totalLateMinutes,
                'earlyMinutes' => $totalEarlyMinutes,
            ];
        }

        $periodText = $startDate->translatedFormat('d M Y') . ' - ' . $endDate->translatedFormat('d M Y');
        if ($startDate->isSameMonth($endDate) && $startDate->isSameYear($endDate)) {
            $periodText = $startDate->translatedFormat('d') . ' - ' . $endDate->translatedFormat('d M Y');
        }

        return view('exports.absensi-rekap', [
            'monthName' => $periodText,
            'daysInMonth' => $totalDays,
            'dayHeaders' => $dayHeaders,
            'rekapData' => $rekapData,
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('A1:Z1000')->getFont()->setName('Arial');
                $sheet->getStyle('A1:Z1000')->getFont()->setSize(9);
            },
        ];
    }
}
