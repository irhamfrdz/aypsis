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
    protected $month;
    protected $year;
    protected $search;
    protected $pekerjaan;
    protected $divisi;

    public function __construct($month, $year, $search = null, $pekerjaan = null, $divisi = null)
    {
        $this->month = $month;
        $this->year = $year;
        $this->search = $search;
        $this->pekerjaan = $pekerjaan;
        $this->divisi = $divisi;
    }

    public function view(): View
    {
        // Prevent execution timeout
        set_time_limit(900);

        $startDate = Carbon::createFromDate($this->year, $this->month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($this->year, $this->month, 1)->endOfMonth();
        $daysInMonth = $startDate->daysInMonth;

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

        // Fetch all attendance logs for the month
        $allLogs = Absensi::whereBetween('waktu', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->get()
            ->groupBy('karyawan_id');

        $rekapData = [];
        
        // Pre-calculate weekend status and day names to avoid instantiating Carbon in loops
        $daysData = [];
        $normalDays = 0;
        $dayHeaders = [];
        
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::createFromDate($this->year, $this->month, $day);
            $isWeekend = $date->isWeekend();
            if (!$isWeekend) {
                $normalDays++;
            }
            $daysData[$day] = [
                'isWeekend' => $isWeekend,
            ];
            $dayHeaders[$day] = [
                'date' => $day,
                'dayName' => $date->minDayName
            ];
        }

        // Performance Optimization: Cache base timestamps to avoid string conversion overhead
        $limitInTime = strtotime('08:00:00');
        $limitOutTime = strtotime('17:00:00');

        foreach ($karyawans as $karyawan) {
            $logs = $allLogs->get($karyawan->id, collect());
            $logsByDay = $logs->groupBy(function ($item) {
                // Parse date index safely
                if ($item->waktu instanceof Carbon) {
                    return $item->waktu->day;
                }
                return Carbon::parse($item->waktu)->day;
            });

            $dailyStatus = [];
            $riilDays = 0;
            $totalLateMinutes = 0;
            $totalEarlyMinutes = 0;

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $isWeekend = $daysData[$day]['isWeekend'];
                $dayLogs = $logsByDay->get($day, collect());

                if ($dayLogs->isEmpty()) {
                    if ($isWeekend) {
                        $dailyStatus[$day] = '';
                    } else {
                        $dailyStatus[$day] = 'A';
                    }
                } else {
                    $riilDays++;
                    
                    // Sort logs by time
                    $sortedLogs = $dayLogs->sortBy('waktu');
                    
                    $firstLog = $sortedLogs->first();
                    $lastLog = $sortedLogs->count() > 1 ? $sortedLogs->last() : null;

                    $clockIn = $firstLog->waktu instanceof Carbon ? $firstLog->waktu : Carbon::parse($firstLog->waktu);
                    $clockOut = $lastLog ? ($lastLog->waktu instanceof Carbon ? $lastLog->waktu : Carbon::parse($lastLog->waktu)) : null;

                    $statusSymbol = '';
                    $inTimeStr = $clockIn->format('H:i:s');

                    // Calculate lateness
                    if ($inTimeStr > '08:00:00') {
                        $statusSymbol = '<';
                        $diff = strtotime($inTimeStr) - $limitInTime;
                        if ($diff > 0) {
                            $totalLateMinutes += round($diff / 60);
                        }
                    }

                    // Calculate early departure
                    if ($clockOut) {
                        $outTimeStr = $clockOut->format('H:i:s');
                        if ($outTimeStr < '17:00:00') {
                            $statusSymbol = $statusSymbol === '<' ? '< >' : '>';
                            $diff = $limitOutTime - strtotime($outTimeStr);
                            if ($diff > 0) {
                                $totalEarlyMinutes += round($diff / 60);
                            }
                        }
                    }

                    $dailyStatus[$day] = $statusSymbol;
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

        return view('exports.absensi-rekap', [
            'monthName' => Carbon::createFromDate($this->year, $this->month, 1)->translatedFormat('F Y'),
            'daysInMonth' => $daysInMonth,
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
