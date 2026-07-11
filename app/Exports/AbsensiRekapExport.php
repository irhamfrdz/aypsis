<?php

namespace App\Exports;

use App\Models\Absensi;
use App\Models\Karyawan;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;

class AbsensiRekapExport extends StringValueBinder implements FromArray, WithCustomValueBinder
{
    protected $startDate;
    protected $endDate;
    protected $search;
    protected $pekerjaan;
    protected $divisi;
    protected $cabang;

    protected $totalDays;
    protected $dayHeaders;
    protected $rekapData;
    protected $periodText;

    public function __construct($startDate, $endDate, $search = null, $pekerjaan = null, $divisi = null, $cabang = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->search = $search;
        $this->pekerjaan = $pekerjaan;
        $this->divisi = $divisi;
        $this->cabang = $cabang;

        $this->prepareData();
    }

    protected function prepareData()
    {
        // Prevent execution timeout and memory exhaustion for massive exports
        set_time_limit(900);
        ini_set('memory_limit', '-1');

        $startDate = Carbon::parse($this->startDate)->startOfDay();
        $endDate = Carbon::parse($this->endDate)->endOfDay();
        
        $this->totalDays = $startDate->diffInDays($endDate) + 1;

        $this->periodText = $startDate->translatedFormat('d M Y') . ' - ' . $endDate->translatedFormat('d M Y');
        if ($startDate->isSameMonth($endDate) && $startDate->isSameYear($endDate)) {
            $this->periodText = $startDate->translatedFormat('d') . ' - ' . $endDate->translatedFormat('d M Y');
        }

        $karyawansQuery = Karyawan::query()->whereNull('tanggal_berhenti');
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
        if (!empty($this->cabang)) {
            $karyawansQuery->where('cabang', $this->cabang);
        }
        $karyawans = $karyawansQuery->orderBy('nama_lengkap')->get();

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

        $this->rekapData = [];
        
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
        $this->dayHeaders = [];
        
        for ($i = 0; $i < $this->totalDays; $i++) {
            $date = $startDate->copy()->addDays($i);
            $dateString = $date->toDateString();
            $isWeekend = $date->isWeekend();
            if (!$isWeekend) {
                $normalDays++;
            }
            $daysData[$dateString] = [
                'isWeekend' => $isWeekend,
                'dateString' => $dateString,
            ];
            $this->dayHeaders[$dateString] = [
                'date' => $date->format('d/m'),
                'dayName' => $dayMap[$date->format('l')],
                'isWeekend' => $isWeekend
            ];
        }

        $limitInTime = strtotime('08:00:00');
        $limitOutTime = strtotime('17:00:00');

        foreach ($karyawans as $karyawan) {
            $logs = $allLogs->get($karyawan->id, collect());
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
                    
                    $inTimeStr = $log->waktu_masuk ? substr($log->waktu_masuk, 11, 5) : '-';
                    $outTimeStr = $log->waktu_pulang ? substr($log->waktu_pulang, 11, 5) : '-';

                    if ($inTimeStr !== '-' && $inTimeStr > '08:00') {
                        $diff = strtotime($inTimeStr.':00') - $limitInTime;
                        if ($diff > 0) {
                            $totalLateMinutes += round($diff / 60);
                        }
                    }

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

            $this->rekapData[] = [
                'karyawan' => $karyawan,
                'dailyStatus' => $dailyStatus,
                'normalDays' => $normalDays,
                'riilDays' => $riilDays,
                'absenDays' => $absenDays,
                'lateMinutes' => $totalLateMinutes,
                'earlyMinutes' => $totalEarlyMinutes,
            ];
        }
    }

    public function array(): array
    {
        $rows = [];
        
        $rows[] = ['REKAPITULASI ABSENSI BULANAN'];
        $rows[] = ['Periode: ' . $this->periodText];
        $rows[] = [];
        
        $header1 = ['Nama', 'No. ID'];
        foreach ($this->dayHeaders as $h) {
            $header1[] = $h['date'];
        }
        $header1 = array_merge($header1, ['Normal Hari', 'Absen Hari', 'Trlmbt Menit', 'Plg. Cpt Menit', 'Lmbr Menit', 'Jml. Ijin', 'D. Luar']);
        $rows[] = $header1;
        
        $header2 = ['', ''];
        foreach ($this->dayHeaders as $h) {
            $header2[] = $h['dayName'];
        }
        $header2 = array_merge($header2, ['', '', '', '', '', '', '']);
        $rows[] = $header2;
        
        foreach ($this->rekapData as $data) {
            $row = [
                 $data['karyawan']->nama_lengkap . ' (' . $data['karyawan']->nik . ')',
                 $data['karyawan']->nik
            ];
            foreach ($this->dayHeaders as $date => $h) {
                 $row[] = $data['dailyStatus'][$date];
            }
            $row[] = $data['normalDays'];
            $row[] = $data['absenDays'];
            $row[] = $data['lateMinutes'] ?: '';
            $row[] = $data['earlyMinutes'] ?: '';
            $row[] = '';
            $row[] = '';
            $row[] = '';
            $rows[] = $row;
        }
        
        $rows[] = [];
        $rows[] = ['Keterangan: Normal="", Absent="A", Format Jam="Masuk - Pulang"'];
        
        return $rows;
    }

}
