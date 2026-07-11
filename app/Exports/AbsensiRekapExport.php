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
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;

class AbsensiRekapExport extends StringValueBinder implements FromArray, WithCustomValueBinder, WithEvents
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
        
        // Row 1: Empty (use [''] instead of [] to prevent Laravel Excel from skipping it)
        $rows[] = [''];
        
        // Row 2: Period Text (Centered over dates, will start at Column C index 2)
        $periodRow = ['', ''];
        $periodRow[] = 'Periode: ' . $this->periodText;
        for ($i = 1; $i < $this->totalDays; $i++) {
            $periodRow[] = '';
        }
        $rows[] = $periodRow;
        
        // Row 3: Empty (use [''] instead of [] to prevent Laravel Excel from skipping it)
        $rows[] = [''];
        
        // Row 4: Header 1
        $header1 = ['Nama', 'No. ID'];
        foreach ($this->dayHeaders as $h) {
            $header1[] = $h['date'];
        }
        $header1 = array_merge($header1, ['Normal Hari', 'Absen Hari', 'Trlmbt Menit', 'Plg. Cpt Menit', 'Lmbr Menit', 'Jml. Ijin', 'D. Luar']);
        $rows[] = $header1;
        
        // Row 5: Header 2
        $header2 = ['', ''];
        foreach ($this->dayHeaders as $h) {
            $header2[] = $h['dayName'];
        }
        $header2 = array_merge($header2, ['', '', '', '', '', '', '']);
        $rows[] = $header2;
        
        // Data Rows (Starting at row 6)
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
        
        $rows[] = [''];
        $rows[] = ['Keterangan: Normal="", Absent="A", Format Jam="Masuk - Pulang"'];
        
        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastColIndex = 2 + $this->totalDays + 7;
                $lastColLetter = Coordinate::stringFromColumnIndex($lastColIndex);
                $totalRows = count($this->rekapData) + 5; 

                // Set Default Styles for the whole sheet (Bypasses range-styling memory bugs)
                $defaultStyle = $sheet->getParent()->getDefaultStyle();
                $defaultStyle->getFont()->setName('Arial')->setSize(9);
                $defaultStyle->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
                $defaultStyle->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                $defaultStyle->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
                $defaultStyle->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
                $defaultStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $defaultStyle->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                // Row 1-3: Clear borders and styling (so it doesn't look boxed)
                $sheet->getStyle('A1:' . $lastColLetter . '3')->getBorders()->getLeft()->setBorderStyle(Border::BORDER_NONE);
                $sheet->getStyle('A1:' . $lastColLetter . '3')->getBorders()->getRight()->setBorderStyle(Border::BORDER_NONE);
                $sheet->getStyle('A1:' . $lastColLetter . '3')->getBorders()->getTop()->setBorderStyle(Border::BORDER_NONE);
                $sheet->getStyle('A1:' . $lastColLetter . '3')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_NONE);

                // Row 2: Merge and Center Period Text over the date columns
                $startPeriodCol = 'C';
                $endPeriodCol = Coordinate::stringFromColumnIndex(2 + $this->totalDays);
                $sheet->mergeCells($startPeriodCol . '2:' . $endPeriodCol . '2');
                $sheet->getStyle('C2')->getFont()->setBold(true);

                // Merging header cells vertically (Row 4 & 5)
                $sheet->mergeCells('A4:A5');
                $sheet->mergeCells('B4:B5');

                $summaryStart = 3 + $this->totalDays;
                for ($i = 0; $i < 7; $i++) {
                    $col = Coordinate::stringFromColumnIndex($summaryStart + $i);
                    $sheet->mergeCells($col . '4:' . $col . '5');
                }

                // Style headers cell-by-cell (Row 4 & 5 only)
                for ($row = 4; $row <= 5; $row++) {
                    for ($colIdx = 1; $colIdx <= $lastColIndex; $colIdx++) {
                        $col = Coordinate::stringFromColumnIndex($colIdx);
                        $cell = $col . $row;
                        $sheet->getStyle($cell)->getFont()->setBold(true);
                        $sheet->getStyle($cell)->getFill()
                              ->setFillType(Fill::FILL_SOLID)
                              ->getStartColor()->setARGB('FFF3F4F6');
                        $sheet->getStyle($cell)->getAlignment()->setWrapText(true);
                    }
                }

                // Column widths & Alignments
                $sheet->getColumnDimension('A')->setWidth(30);
                $sheet->getColumnDimension('B')->setWidth(15);
                for ($i = 1; $i <= $this->totalDays; $i++) {
                    $col = Coordinate::stringFromColumnIndex(2 + $i);
                    $sheet->getColumnDimension($col)->setWidth(12);
                }

                if (count($this->rekapData) > 0) {
                    $gridRange = 'C6:' . $lastColLetter . $totalRows;

                    // Column A (Nama Karyawan): Left-align only
                    $sheet->getStyle('A6:A' . $totalRows)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                    // 1. Weekend Rule using Excel Formula: OR(C$5="Sab", C$5="Min")
                    $weekendRule = new Conditional();
                    $weekendRule->setConditionType(Conditional::CONDITION_EXPRESSION);
                    $weekendRule->addCondition('OR(C$5="Sab",C$5="Min")');
                    $weekendRule->getStyle()->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setARGB('FFD1D5DB'); // Gray

                    // 2. Absence Rule: cell value equals "A"
                    $absenceRule = new Conditional();
                    $absenceRule->setConditionType(Conditional::CONDITION_CELLIS);
                    $absenceRule->setOperatorType(Conditional::OPERATOR_EQUAL);
                    $absenceRule->addCondition('"A"');
                    $absenceRule->getStyle()->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setARGB('FFFECACA'); // Light red

                    // Apply conditional styles
                    $sheet->setConditionalStyles($gridRange, [$weekendRule, $absenceRule]);

                    // Remove borders and formatting for the legend rows at the bottom
                    $lastDataRow = $totalRows;
                    $legendStart = $lastDataRow + 1;
                    $legendEnd = $lastDataRow + 2;
                    $sheet->getStyle('A' . $legendStart . ':' . $lastColLetter . $legendEnd)->getBorders()->getLeft()->setBorderStyle(Border::BORDER_NONE);
                    $sheet->getStyle('A' . $legendStart . ':' . $lastColLetter . $legendEnd)->getBorders()->getRight()->setBorderStyle(Border::BORDER_NONE);
                    $sheet->getStyle('A' . $legendStart . ':' . $lastColLetter . $legendEnd)->getBorders()->getTop()->setBorderStyle(Border::BORDER_NONE);
                    $sheet->getStyle('A' . $legendStart . ':' . $lastColLetter . $legendEnd)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_NONE);
                    
                    // Left-align legend text
                    $sheet->getStyle('A' . $legendEnd)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                }
            },
        ];
    }
}
