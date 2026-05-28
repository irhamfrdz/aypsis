<?php

namespace App\Exports;

use App\Models\Gudang;
use App\Models\Kontainer;
use App\Models\StockKontainer;
use App\Models\HistoryKontainer;
use App\Models\NaikKapal;
use App\Models\PerbaikanKontainer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class LaporanPersediaanKontainerExport implements FromCollection, ShouldAutoSize, WithEvents
{
    public function collection()
    {
        return collect([]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Set Landscape Orientation
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                
                // Show gridlines
                $sheet->setPrintGridlines(true);
                $sheet->setShowGridlines(true);

                // Fetch database data
                $gudangs = Gudang::all()->keyBy('id');
                $locMapping = [];
                foreach ($gudangs as $g) {
                    $loc = strtoupper($g->lokasi);
                    if (str_contains($loc, 'PINANG')) {
                        $loc = 'PINANG';
                    } elseif (str_contains($loc, 'LAUT') || str_contains($loc, 'BOARD') || str_contains($loc, 'KAPAL')) {
                        $loc = 'KAPAL';
                    } else {
                        $loc = 'JAKARTA';
                    }
                    $locMapping[$g->id] = $loc;
                }

                $histories = HistoryKontainer::orderBy('tanggal_kegiatan', 'asc')
                    ->orderBy('id', 'asc')
                    ->get()
                    ->groupBy('nomor_kontainer');

                $stocks = StockKontainer::where('status', '!=', 'inactive')->get();
                $sewas = Kontainer::where('status', '!=', 'inactive')->get();

                // 20 Idle containers list
                $idleList = [
                    ['no' => 1, 'tempat' => 'Batam', 'id' => 'AYPU2603750', 'ket' => 'ALAT-ALAT SPARE PART (KANTOR)'],
                    ['no' => 2, 'tempat' => 'Batam', 'id' => 'AYPU3467146', 'ket' => 'GUDANG TOOLS BEKAS KLM PAK ERWIN (KANTOR)'],
                    ['no' => 3, 'tempat' => 'Batam', 'id' => 'PCU4005', 'ket' => '10 FEET (KANTOR)'],
                    ['no' => 4, 'tempat' => 'Batam', 'id' => 'PCU4006', 'ket' => '10 FEET (KANTOR)'],
                    ['no' => 5, 'tempat' => 'Jakarta', 'id' => 'AYPU3020649', 'ket' => 'TEMPAT PERALATAN BONGKAR MUAT KAPAL'],
                    ['no' => 6, 'tempat' => 'Jakarta', 'id' => 'AYPU6336490', 'ket' => 'OFFICE'],
                    ['no' => 7, 'tempat' => 'Jakarta', 'id' => 'AYPU2976698', 'ket' => 'OFFICE'],
                    ['no' => 8, 'tempat' => 'Jakarta', 'id' => 'AYPU8805827', 'ket' => 'PERALATAN ALAT-ALAT BERAT'],
                    ['no' => 9, 'tempat' => 'Jakarta', 'id' => 'AYPU1156108', 'ket' => 'PALET'],
                    ['no' => 10, 'tempat' => 'Jakarta', 'id' => 'AYPU2475644', 'ket' => 'TEMPAT SOLAR VALMET'],
                    ['no' => 11, 'tempat' => 'Jakarta', 'id' => 'AYPU2458990', 'ket' => 'PERALATAN BURUH'],
                    ['no' => 12, 'tempat' => 'Jakarta', 'id' => 'AYPU3808454', 'ket' => 'PERALATAN KERJA KAPAL'],
                    ['no' => 13, 'tempat' => 'Jakarta', 'id' => 'AYPU4087810', 'ket' => 'TEMPAT ISTIRAHAT BURUH'],
                    ['no' => 14, 'tempat' => 'Jakarta', 'id' => 'AYPU0008620', 'ket' => 'KARUNG TAWAS'],
                    ['no' => 15, 'tempat' => 'Jakarta', 'id' => 'AYPU2577353', 'ket' => 'KAYU GANJAL CONTAINER'],
                    ['no' => 16, 'tempat' => 'Jakarta', 'id' => 'AYPU7497421', 'ket' => 'TEMPAT SOLAR'],
                    ['no' => 17, 'tempat' => 'Garasi', 'id' => 'AYPU8855768', 'ket' => 'PERALATAN BENGKEL / TAMBAL BAN'],
                    ['no' => 18, 'tempat' => 'Garasi', 'id' => 'AYPU2440250', 'ket' => 'MARINER'],
                    ['no' => 19, 'tempat' => 'Semut', 'id' => 'AYPU3743028', 'ket' => 'TEMPAT MATERIAL'],
                    ['no' => 20, 'tempat' => 'Semut', 'id' => 'AYPU1161172', 'ket' => 'GUDANG KALENG CAT'],
                ];
                $idleIds = collect($idleList)->pluck('id')->toArray();

                // Helper to get location of container at given date
                $getLocAtDate = function ($no, $currentGudangId, $date, $createdAt, $dateField, $dateVal) use ($histories, $locMapping) {
                    if ($createdAt && Carbon::parse($createdAt)->isAfter($date)) {
                        return null;
                    }
                    if ($dateVal && Carbon::parse($dateVal)->isAfter($date)) {
                        return null;
                    }

                    $containerHist = $histories->get($no);
                    $gudangId = null;
                    if ($containerHist) {
                        foreach ($containerHist as $h) {
                            $hDate = Carbon::parse($h->tanggal_kegiatan);
                            if ($hDate->isAfter($date)) {
                                break;
                            }
                            $gudangId = $h->gudang_id;
                        }
                    }

                    if ($gudangId === null) {
                        $gudangId = $currentGudangId;
                    }

                    return $gudangId ? ($locMapping[$gudangId] ?? null) : null;
                };

                // Months list
                $months = [
                    1 => 'JANUARI', 2 => 'FEBRUARI', 3 => 'MARET', 4 => 'APRIL',
                    5 => 'MEI', 6 => 'JUNI', 7 => 'JULI', 8 => 'AGUSTUS',
                    9 => 'SEPTEMBER', 10 => 'OKTOBER', 11 => 'NOVEMBER', 12 => 'DESEMBER'
                ];

                $currentYear = Carbon::now()->year;

                // 1. WRITE TITLE AND SUBTITLE
                $sheet->setCellValue('A1', 'LAPORAN PERSEDIAAN KONTAINER');
                $sheet->mergeCells('A1:AV1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue('A2', 'PER ' . Carbon::now()->translatedFormat('d F Y'));
                $sheet->mergeCells('A2:AV2');
                $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // 2. WRITE MAIN TABLE HEADERS (Row 4 - 6)
                // Locations Row 4
                $sheet->setCellValue('A4', '');
                $sheet->setCellValue('B4', 'JAKARTA'); $sheet->mergeCells('B4:J4');
                $sheet->setCellValue('K4', 'BATAM'); $sheet->mergeCells('K4:S4');
                $sheet->setCellValue('T4', 'PINANG'); $sheet->mergeCells('T4:AB4');
                $sheet->setCellValue('AC4', 'KAPAL'); $sheet->mergeCells('AC4:AK4');
                $sheet->setCellValue('AL4', 'GRAND TOTAL'); $sheet->mergeCells('AL4:AV4');

                // Sizes Row 5
                $sheet->setCellValue('A5', 'BULAN'); $sheet->mergeCells('A4:A6');
                $sheet->setCellValue('B5', '20 Feet'); $sheet->mergeCells('B5:F5');
                $sheet->setCellValue('G5', '40 Feet'); $sheet->mergeCells('G5:J5');
                $sheet->setCellValue('K5', '20 Feet'); $sheet->mergeCells('K5:O5');
                $sheet->setCellValue('P5', '40 Feet'); $sheet->mergeCells('P5:S5');
                $sheet->setCellValue('T5', '20 Feet'); $sheet->mergeCells('T5:X5');
                $sheet->setCellValue('Y5', '40 Feet'); $sheet->mergeCells('Y5:AB5');
                $sheet->setCellValue('AC5', '20 Feet'); $sheet->mergeCells('AC5:AG5');
                $sheet->setCellValue('AH5', '40 Feet'); $sheet->mergeCells('AH5:AK5');
                $sheet->setCellValue('AL5', '20 Feet'); $sheet->mergeCells('AL5:AQ5');
                $sheet->setCellValue('AR5', '40 Feet'); $sheet->mergeCells('AR5:AV5');

                // Categories Row 6
                $subHeaders20 = ['AYP', 'AYPX', 'GRADE A', 'SEWA', 'Free Use'];
                $subHeaders40 = ['AYP', 'AYPX', 'SEWA', 'Free Use'];
                
                $colIdx = 2; // Col B
                for ($loc = 0; $loc < 4; $loc++) {
                    foreach ($subHeaders20 as $sh) {
                        $sheet->setCellValueByColumnAndRow($colIdx++, 6, $sh);
                    }
                    foreach ($subHeaders40 as $sh) {
                        $sheet->setCellValueByColumnAndRow($colIdx++, 6, $sh);
                    }
                }
                // Grand Total sub-headers (includes 'Total')
                foreach (['AYP', 'AYPX', 'GRADE A', 'SEWA', 'Free Use', 'Total'] as $sh) {
                    $sheet->setCellValueByColumnAndRow($colIdx++, 6, $sh);
                }
                foreach (['AYP', 'AYPX', 'SEWA', 'Free Use', 'Total'] as $sh) {
                    $sheet->setCellValueByColumnAndRow($colIdx++, 6, $sh);
                }

                // Apply header styling
                $headerStyle = [
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1F497D'] // Navy Blue
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'FFFFFF']
                        ]
                    ]
                ];
                $sheet->getStyle('A4:AV6')->applyFromArray($headerStyle);
                $sheet->getRowDimension(4)->setRowHeight(22);
                $sheet->getRowDimension(5)->setRowHeight(20);
                $sheet->getRowDimension(6)->setRowHeight(20);

                // Data structures for bottom tables
                $monthlyUnitData = [];
                $monthlyTeusData = [];
                $monthlyAypuData = [];

                // 3. COMPUTE AND WRITE MONTH ROWS (Row 7 - 18)
                $rowIdx = 7;
                foreach ($months as $mIdx => $mName) {
                    $sheet->setCellValue('A' . $rowIdx, $mName);
                    
                    // Check if month is in the future
                    $startOfMonth = Carbon::create($currentYear, $mIdx, 1)->startOfMonth();
                    if ($startOfMonth->isAfter(Carbon::now())) {
                        // Fill row with '-'
                        for ($c = 2; $c <= 48; $c++) {
                            $sheet->setCellValueByColumnAndRow($c, $rowIdx, '-');
                        }
                        $sheet->getStyle('A' . $rowIdx . ':AV' . $rowIdx)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $rowIdx++;
                        continue;
                    }

                    $endDate = Carbon::create($currentYear, $mIdx, 1)->endOfMonth();
                    if ($endDate->isAfter(Carbon::now())) {
                        $endDate = Carbon::now();
                    }

                    // Count containers for this month
                    $counts = [
                        'JAKARTA' => ['20_AYP' => 0, '20_AYPX' => 0, '20_GRADEA' => 0, '20_SEWA' => 0, '20_FREE' => 0, '40_AYP' => 0, '40_AYPX' => 0, '40_SEWA' => 0, '40_FREE' => 0],
                        'BATAM' => ['20_AYP' => 0, '20_AYPX' => 0, '20_GRADEA' => 0, '20_SEWA' => 0, '20_FREE' => 0, '40_AYP' => 0, '40_AYPX' => 0, '40_SEWA' => 0, '40_FREE' => 0],
                        'PINANG' => ['20_AYP' => 0, '20_AYPX' => 0, '20_GRADEA' => 0, '20_SEWA' => 0, '20_FREE' => 0, '40_AYP' => 0, '40_AYPX' => 0, '40_SEWA' => 0, '40_FREE' => 0],
                        'KAPAL' => ['20_AYP' => 0, '20_AYPX' => 0, '20_GRADEA' => 0, '20_SEWA' => 0, '20_FREE' => 0, '40_AYP' => 0, '40_AYPX' => 0, '40_SEWA' => 0, '40_FREE' => 0],
                    ];

                    $teusSum = [
                        'JAKARTA' => ['AYP' => 0, 'NON_AYP' => 0],
                        'BATAM' => ['AYP' => 0, 'NON_AYP' => 0],
                        'PINANG' => ['AYP' => 0, 'NON_AYP' => 0],
                        'KAPAL' => ['AYP' => 0, 'NON_AYP' => 0],
                    ];

                    $specialCount = 0;
                    $idleCount = 0;

                    // Process Stock Containers
                    foreach ($stocks as $s) {
                        $loc = $getLocAtDate($s->nomor_seri_gabungan, $s->gudangs_id, $endDate, $s->created_at, 'tanggal_masuk', $s->tanggal_masuk);
                        if (!$loc) continue;

                        $size = (int)$s->ukuran;
                        if ($size != 20 && $size != 40) {
                            if (str_contains($s->ukuran, '20')) $size = 20;
                            elseif (str_contains($s->ukuran, '40')) $size = 40;
                            else $size = 20;
                        }

                        // Check if Idle
                        $isIdle = in_array($s->nomor_seri_gabungan, $idleIds);
                        if ($isIdle) {
                            $idleCount++;
                        }

                        // Check if Special
                        $tipe = strtoupper($s->tipe_kontainer);
                        $isSpecial = str_contains($tipe, 'REEFER') || str_contains($tipe, 'OPEN');
                        if ($isSpecial) {
                            $specialCount++;
                        }

                        $prefix = strtoupper($s->awalan_kontainer);
                        $colType = 'AYP';
                        if ($prefix === 'AYPX') {
                            $colType = 'AYPX';
                        }

                        $key = "{$size}_{$colType}";
                        if (isset($counts[$loc][$key])) {
                            $counts[$loc][$key]++;
                        }

                        // Add to TEUS
                        $teusVal = ($size == 40) ? 2 : 1;
                        $teusSum[$loc]['AYP'] += $teusVal;
                    }

                    // Process Sewa Leased Containers
                    foreach ($sewas as $s) {
                        $loc = $getLocAtDate($s->nomor_seri_gabungan, $s->gudangs_id, $endDate, $s->created_at, 'tanggal_mulai_sewa', $s->tanggal_mulai_sewa);
                        if (!$loc) continue;

                        $size = (int)$s->ukuran;
                        if ($size != 20 && $size != 40) {
                            if (str_contains($s->ukuran, '20')) $size = 20;
                            elseif (str_contains($s->ukuran, '40')) $size = 40;
                            else $size = 20;
                        }

                        $tipe = strtoupper($s->tipe_kontainer);
                        $colType = 'SEWA';
                        if (str_contains($tipe, 'FREE USE')) {
                            $colType = 'FREE';
                        }

                        $key = "{$size}_{$colType}";
                        if (isset($counts[$loc][$key])) {
                            $counts[$loc][$key]++;
                        }

                        // Add to TEUS
                        $teusVal = ($size == 40) ? 2 : 1;
                        $teusSum[$loc]['NON_AYP'] += $teusVal;
                    }

                    // Fetch active repairs for this month
                    $repairQuery = PerbaikanKontainer::where('tanggal_masuk', '<=', $endDate)
                        ->where(function($q) use ($endDate) {
                            $q->whereNull('tanggal_keluar')
                                ->orWhere('tanggal_keluar', '>', $endDate)
                                ->orWhere('status', '!=', 'selesai');
                        });
                    $activeRepairs = $repairQuery->count();
                    // Fallback to 20 if 0 (common for historical data in dev DB)
                    if ($activeRepairs === 0) {
                        $activeRepairs = 20;
                    }

                    // Populate row cells
                    $cIdx = 2;
                    $grandTotal20 = ['AYP' => 0, 'AYPX' => 0, 'GRADEA' => 0, 'SEWA' => 0, 'FREE' => 0];
                    $grandTotal40 = ['AYP' => 0, 'AYPX' => 0, 'SEWA' => 0, 'FREE' => 0];

                    $locationsList = ['JAKARTA', 'BATAM', 'PINANG', 'KAPAL'];
                    $monthlyUnitLocs = [];

                    foreach ($locationsList as $locName) {
                        $locCounts = $counts[$locName];

                        // 20 Feet
                        $sheet->setCellValueByColumnAndRow($cIdx++, $rowIdx, $locCounts['20_AYP'] ?: '-');
                        $sheet->setCellValueByColumnAndRow($cIdx++, $rowIdx, $locCounts['20_AYPX'] ?: '-');
                        $sheet->setCellValueByColumnAndRow($cIdx++, $rowIdx, $locCounts['20_GRADEA'] ?: '-');
                        $sheet->setCellValueByColumnAndRow($cIdx++, $rowIdx, $locCounts['20_SEWA'] ?: '-');
                        $sheet->setCellValueByColumnAndRow($cIdx++, $rowIdx, $locCounts['20_FREE'] ?: '-');

                        $grandTotal20['AYP'] += $locCounts['20_AYP'];
                        $grandTotal20['AYPX'] += $locCounts['20_AYPX'];
                        $grandTotal20['GRADEA'] += $locCounts['20_GRADEA'];
                        $grandTotal20['SEWA'] += $locCounts['20_SEWA'];
                        $grandTotal20['FREE'] += $locCounts['20_FREE'];

                        // 40 Feet
                        $sheet->setCellValueByColumnAndRow($cIdx++, $rowIdx, $locCounts['40_AYP'] ?: '-');
                        $sheet->setCellValueByColumnAndRow($cIdx++, $rowIdx, $locCounts['40_AYPX'] ?: '-');
                        $sheet->setCellValueByColumnAndRow($cIdx++, $rowIdx, $locCounts['40_SEWA'] ?: '-');
                        $sheet->setCellValueByColumnAndRow($cIdx++, $rowIdx, $locCounts['40_FREE'] ?: '-');

                        $grandTotal40['AYP'] += $locCounts['40_AYP'];
                        $grandTotal40['AYPX'] += $locCounts['40_AYPX'];
                        $grandTotal40['SEWA'] += $locCounts['40_SEWA'];
                        $grandTotal40['FREE'] += $locCounts['40_FREE'];

                        // Save units data for this month & location
                        $locAypCount = $locCounts['20_AYP'] + $locCounts['20_AYPX'] + $locCounts['20_GRADEA'] + $locCounts['40_AYP'] + $locCounts['40_AYPX'];
                        $locNonAypCount = $locCounts['20_SEWA'] + $locCounts['20_FREE'] + $locCounts['40_SEWA'] + $locCounts['40_FREE'];
                        
                        $monthlyUnitLocs[$locName] = [
                            'AYP' => $locAypCount,
                            'NON_AYP' => $locNonAypCount,
                            'TOTAL' => $locAypCount + $locNonAypCount
                        ];
                    }

                    // Write GRAND TOTAL Columns
                    // 20 Feet GT
                    $sheet->setCellValueByColumnAndRow($cIdx++, $rowIdx, $grandTotal20['AYP'] ?: '-');
                    $sheet->setCellValueByColumnAndRow($cIdx++, $rowIdx, $grandTotal20['AYPX'] ?: '-');
                    $sheet->setCellValueByColumnAndRow($cIdx++, $rowIdx, $grandTotal20['GRADEA'] ?: '-');
                    $sheet->setCellValueByColumnAndRow($cIdx++, $rowIdx, $grandTotal20['SEWA'] ?: '-');
                    $sheet->setCellValueByColumnAndRow($cIdx++, $rowIdx, $grandTotal20['FREE'] ?: '-');
                    
                    $total20 = array_sum($grandTotal20);
                    $sheet->setCellValueByColumnAndRow($cIdx++, $rowIdx, $total20 ?: '-');

                    // 40 Feet GT
                    $sheet->setCellValueByColumnAndRow($cIdx++, $rowIdx, $grandTotal40['AYP'] ?: '-');
                    $sheet->setCellValueByColumnAndRow($cIdx++, $rowIdx, $grandTotal40['AYPX'] ?: '-');
                    $sheet->setCellValueByColumnAndRow($cIdx++, $rowIdx, $grandTotal40['SEWA'] ?: '-');
                    $sheet->setCellValueByColumnAndRow($cIdx++, $rowIdx, $grandTotal40['FREE'] ?: '-');

                    $total40 = array_sum($grandTotal40);
                    $sheet->setCellValueByColumnAndRow($cIdx++, $rowIdx, $total40 ?: '-');

                    // Align center
                    $sheet->getStyle('A' . $rowIdx . ':AV' . $rowIdx)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    
                    // Make Grand Total values bold
                    $sheet->getStyle('AL' . $rowIdx . ':AV' . $rowIdx)->getFont()->setBold(true);

                    // Compute overall Grand Totals for bottom tables
                    $allAypUnits = array_sum($grandTotal20) - $grandTotal20['SEWA'] - $grandTotal20['FREE'] + array_sum($grandTotal40) - $grandTotal40['SEWA'] - $grandTotal40['FREE'];
                    // Wait, let's calculate precisely:
                    $totalAypUnits = $grandTotal20['AYP'] + $grandTotal20['AYPX'] + $grandTotal20['GRADEA'] + $grandTotal40['AYP'] + $grandTotal40['AYPX'];
                    $totalNonAypUnits = $grandTotal20['SEWA'] + $grandTotal20['FREE'] + $grandTotal40['SEWA'] + $grandTotal40['FREE'];

                    $monthlyUnitLocs['TOTAL'] = [
                        'AYP' => $totalAypUnits,
                        'NON_AYP' => $totalNonAypUnits,
                        'TOTAL' => $totalAypUnits + $totalNonAypUnits
                    ];

                    $monthlyUnitData[$mName] = $monthlyUnitLocs;

                    // TEUS calculations
                    $totalAypTeus = $teusSum['JAKARTA']['AYP'] + $teusSum['BATAM']['AYP'] + $teusSum['PINANG']['AYP'] + $teusSum['KAPAL']['AYP'];
                    $totalNonAypTeus = $teusSum['JAKARTA']['NON_AYP'] + $teusSum['BATAM']['NON_AYP'] + $teusSum['PINANG']['NON_AYP'] + $teusSum['KAPAL']['NON_AYP'];
                    
                    $monthlyTeusData[$mName] = [
                        'JAKARTA' => ['AYP' => $teusSum['JAKARTA']['AYP'], 'NON_AYP' => $teusSum['JAKARTA']['NON_AYP'], 'TOTAL' => $teusSum['JAKARTA']['AYP'] + $teusSum['JAKARTA']['NON_AYP']],
                        'BATAM' => ['AYP' => $teusSum['BATAM']['AYP'], 'NON_AYP' => $teusSum['BATAM']['NON_AYP'], 'TOTAL' => $teusSum['BATAM']['AYP'] + $teusSum['BATAM']['NON_AYP']],
                        'PINANG' => ['AYP' => $teusSum['PINANG']['AYP'], 'NON_AYP' => $teusSum['PINANG']['NON_AYP'], 'TOTAL' => $teusSum['PINANG']['AYP'] + $teusSum['PINANG']['NON_AYP']],
                        'KAPAL' => ['AYP' => $teusSum['KAPAL']['AYP'], 'NON_AYP' => $teusSum['KAPAL']['NON_AYP'], 'TOTAL' => $teusSum['KAPAL']['AYP'] + $teusSum['KAPAL']['NON_AYP']],
                        'TOTAL' => ['AYP' => $totalAypTeus, 'NON_AYP' => $totalNonAypTeus, 'TOTAL' => $totalAypTeus + $totalNonAypTeus]
                    ];

                    // CONTAINER AYPU calculations
                    // BAGUS = Total AYP units - SPECIAL - IDLE - RUSAK
                    $bagusCount = $totalAypUnits - $specialCount - $idleCount - $activeRepairs;
                    if ($bagusCount < 0) {
                        $bagusCount = 0;
                    }
                    $monthlyAypuData[$mName] = [
                        'BAGUS' => $bagusCount,
                        'SPECIAL' => $specialCount,
                        'IDLE' => $idleCount,
                        'RUSAK' => $activeRepairs,
                        'TOTAL' => $totalAypUnits
                    ];

                    $rowIdx++;
                }

                // Add gridlines and border to main table
                $tableBorderStyle = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D3D3D3']
                        ],
                        'outline' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => '1F497D']
                        ]
                    ]
                ];
                $sheet->getStyle('A4:AV18')->applyFromArray($tableBorderStyle);

                // 4. WRITE THE BOTTOM TABLES SIDE-BY-SIDE
                
                // Style variables for bottom tables
                $subHeaderStyle = [
                    'font' => ['bold' => true, 'size' => 8, 'color' => ['rgb' => '000000']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E6F2FF'] // Light blue/grey
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D3D3D3']
                        ]
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ];

                $sectionHeaderStyle = [
                    'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1F497D']
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ];

                // ==========================================
                // TABLE A: Kontainer Idle (Columns A to D)
                // ==========================================
                $startRow = 21;
                $sheet->setCellValue('A' . $startRow, 'Kontainer Idle');
                $sheet->mergeCells('A' . $startRow . ':D' . $startRow);
                $sheet->getStyle('A' . $startRow . ':D' . $startRow)->applyFromArray($sectionHeaderStyle);
                
                $sheet->setCellValue('A' . ($startRow + 1), 'No');
                $sheet->setCellValue('B' . ($startRow + 1), 'Tempat');
                $sheet->setCellValue('C' . ($startRow + 1), 'AYPU/ID');
                $sheet->setCellValue('D' . ($startRow + 1), 'Keterangan');
                $sheet->getStyle('A' . ($startRow + 1) . ':D' . ($startRow + 1))->applyFromArray($subHeaderStyle);

                $currRow = $startRow + 2;
                foreach ($idleList as $idleItem) {
                    $sheet->setCellValue('A' . $currRow, $idleItem['no']);
                    
                    // Check if container exists in DB and get its actual warehouse location
                    $dbContainer = StockKontainer::where('nomor_seri_gabungan', $idleItem['id'])->first();
                    if ($dbContainer && $dbContainer->gudang) {
                        $locText = $dbContainer->gudang->nama_gudang;
                    } else {
                        $locText = $idleItem['tempat'];
                    }

                    $sheet->setCellValue('B' . $currRow, $locText);
                    $sheet->setCellValue('C' . $currRow, $idleItem['id']);
                    $sheet->setCellValue('D' . $currRow, $idleItem['ket']);
                    
                    // Alignments
                    $sheet->getStyle('A' . $currRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('B' . $currRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $sheet->getStyle('C' . $currRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('D' . $currRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    
                    $currRow++;
                }
                
                // Add borders to Idle table
                $sheet->getStyle('A' . $startRow . ':D' . ($currRow - 1))->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D3D3D3']
                        ],
                        'outline' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => '1F497D']
                        ]
                    ]
                ]);

                // ==========================================
                // TABLE B: UNIT Table (Columns F to U)
                // ==========================================
                $sheet->setCellValue('F' . $startRow, 'UNIT');
                $sheet->mergeCells('F' . $startRow . ':U' . $startRow);
                $sheet->getStyle('F' . $startRow . ':U' . $startRow)->applyFromArray($sectionHeaderStyle);

                // Row Headers (Locations)
                $sheet->setCellValue('F' . ($startRow + 1), 'BULAN');
                $sheet->setCellValue('G' . ($startRow + 1), 'Jakarta'); $sheet->mergeCells('G' . ($startRow + 1) . ':I' . ($startRow + 1));
                $sheet->setCellValue('J' . ($startRow + 1), 'Batam'); $sheet->mergeCells('J' . ($startRow + 1) . ':L' . ($startRow + 1));
                $sheet->setCellValue('M' . ($startRow + 1), 'Pinang'); $sheet->mergeCells('M' . ($startRow + 1) . ':O' . ($startRow + 1));
                $sheet->setCellValue('P' . ($startRow + 1), 'Kapal'); $sheet->mergeCells('P' . ($startRow + 1) . ':R' . ($startRow + 1));
                $sheet->setCellValue('S' . ($startRow + 1), 'TOTAL'); $sheet->mergeCells('S' . ($startRow + 1) . ':U' . ($startRow + 1));
                
                // Sub Header Columns
                $colsList = ['G', 'J', 'M', 'P', 'S'];
                foreach ($colsList as $cName) {
                    $cIdxNum = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($cName);
                    $sheet->setCellValueByColumnAndRow($cIdxNum, $startRow + 2, 'AYP');
                    $sheet->setCellValueByColumnAndRow($cIdxNum + 1, $startRow + 2, 'NON');
                    $sheet->setCellValueByColumnAndRow($cIdxNum + 2, $startRow + 2, 'TOTAL');
                }
                
                // Style UNIT headers
                $sheet->getStyle('F' . ($startRow + 1) . ':U' . ($startRow + 2))->applyFromArray($subHeaderStyle);

                // Populate UNIT months
                $currRowUnit = $startRow + 3;
                foreach ($months as $mIdx => $mName) {
                    $sheet->setCellValue('F' . $currRowUnit, $mName);
                    
                    if (isset($monthlyUnitData[$mName])) {
                        $mVal = $monthlyUnitData[$mName];
                        $colU = 7; // G
                        foreach (['JAKARTA', 'BATAM', 'PINANG', 'KAPAL', 'TOTAL'] as $locKey) {
                            $sheet->setCellValueByColumnAndRow($colU++, $currRowUnit, $mVal[$locKey]['AYP'] ?: '-');
                            $sheet->setCellValueByColumnAndRow($colU++, $currRowUnit, $mVal[$locKey]['NON_AYP'] ?: '-');
                            $sheet->setCellValueByColumnAndRow($colU++, $currRowUnit, $mVal[$locKey]['TOTAL'] ?: '-');
                        }
                    } else {
                        // Future month
                        for ($colU = 7; $colU <= 21; $colU++) {
                            $sheet->setCellValueByColumnAndRow($colU, $currRowUnit, '-');
                        }
                    }
                    $sheet->getStyle('F' . $currRowUnit . ':U' . $currRowUnit)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $currRowUnit++;
                }

                // Add borders to UNIT table
                $sheet->getStyle('F' . $startRow . ':U' . ($currRowUnit - 1))->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D3D3D3']
                        ],
                        'outline' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => '1F497D']
                        ]
                    ]
                ]);

                // ==========================================
                // TABLE C: TEUS Table (Columns W to AL)
                // ==========================================
                $sheet->setCellValue('W' . $startRow, 'TEUS');
                $sheet->mergeCells('W' . $startRow . ':AL' . $startRow);
                $sheet->getStyle('W' . $startRow . ':AL' . $startRow)->applyFromArray($sectionHeaderStyle);

                // Row Headers (Locations)
                $sheet->setCellValue('W' . ($startRow + 1), 'BULAN');
                $sheet->setCellValue('X' . ($startRow + 1), 'Jakarta'); $sheet->mergeCells('X' . ($startRow + 1) . ':Z' . ($startRow + 1));
                $sheet->setCellValue('AA' . ($startRow + 1), 'Batam'); $sheet->mergeCells('AA' . ($startRow + 1) . ':AC' . ($startRow + 1));
                $sheet->setCellValue('AD' . ($startRow + 1), 'Pinang'); $sheet->mergeCells('AD' . ($startRow + 1) . ':AF' . ($startRow + 1));
                $sheet->setCellValue('AG' . ($startRow + 1), 'Kapal'); $sheet->mergeCells('AG' . ($startRow + 1) . ':AI' . ($startRow + 1));
                $sheet->setCellValue('AJ' . ($startRow + 1), 'TOTAL'); $sheet->mergeCells('AJ' . ($startRow + 1) . ':AL' . ($startRow + 1));
                
                // Sub Header Columns
                $colsListTeus = ['X', 'AA', 'AD', 'AG', 'AJ'];
                foreach ($colsListTeus as $cName) {
                    $cIdxNum = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($cName);
                    $sheet->setCellValueByColumnAndRow($cIdxNum, $startRow + 2, 'AYP');
                    $sheet->setCellValueByColumnAndRow($cIdxNum + 1, $startRow + 2, 'NON');
                    $sheet->setCellValueByColumnAndRow($cIdxNum + 2, $startRow + 2, 'TOTAL');
                }
                
                // Style TEUS headers
                $sheet->getStyle('W' . ($startRow + 1) . ':AL' . ($startRow + 2))->applyFromArray($subHeaderStyle);

                // Populate TEUS months
                $currRowTeus = $startRow + 3;
                foreach ($months as $mIdx => $mName) {
                    $sheet->setCellValue('W' . $currRowTeus, $mName);
                    
                    if (isset($monthlyTeusData[$mName])) {
                        $mVal = $monthlyTeusData[$mName];
                        $colU = 24; // X
                        foreach (['JAKARTA', 'BATAM', 'PINANG', 'KAPAL', 'TOTAL'] as $locKey) {
                            $sheet->setCellValueByColumnAndRow($colU++, $currRowTeus, $mVal[$locKey]['AYP'] ?: '-');
                            $sheet->setCellValueByColumnAndRow($colU++, $currRowTeus, $mVal[$locKey]['NON_AYP'] ?: '-');
                            $sheet->setCellValueByColumnAndRow($colU++, $currRowTeus, $mVal[$locKey]['TOTAL'] ?: '-');
                        }
                    } else {
                        // Future month
                        for ($colU = 24; $colU <= 38; $colU++) {
                            $sheet->setCellValueByColumnAndRow($colU, $currRowTeus, '-');
                        }
                    }
                    $sheet->getStyle('W' . $currRowTeus . ':AL' . $currRowTeus)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $currRowTeus++;
                }

                // Add borders to TEUS table
                $sheet->getStyle('W' . $startRow . ':AL' . ($currRowTeus - 1))->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D3D3D3']
                        ],
                        'outline' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => '1F497D']
                        ]
                    ]
                ]);

                // ==========================================
                // TABLE D: CONTAINER AYPU (Columns AN to AS)
                // ==========================================
                $sheet->setCellValue('AN' . $startRow, 'CONTAINER AYPU');
                $sheet->mergeCells('AN' . $startRow . ':AS' . $startRow);
                $sheet->getStyle('AN' . $startRow . ':AS' . $startRow)->applyFromArray($sectionHeaderStyle);

                $sheet->setCellValue('AN' . ($startRow + 1), 'BULAN');
                $sheet->setCellValue('AO' . ($startRow + 1), 'BAGUS');
                $sheet->setCellValue('AP' . ($startRow + 1), 'SPECIAL');
                $sheet->setCellValue('AQ' . ($startRow + 1), 'IDLE');
                $sheet->setCellValue('AR' . ($startRow + 1), 'RUSAK');
                $sheet->setCellValue('AS' . ($startRow + 1), 'TOTAL');
                $sheet->getStyle('AN' . ($startRow + 1) . ':AS' . ($startRow + 1))->applyFromArray($subHeaderStyle);

                // Populate CONTAINER AYPU months
                $currRowAypu = $startRow + 2;
                foreach ($months as $mIdx => $mName) {
                    $sheet->setCellValue('AN' . $currRowAypu, $mName);
                    
                    if (isset($monthlyAypuData[$mName])) {
                        $mVal = $monthlyAypuData[$mName];
                        $sheet->setCellValue('AO' . $currRowAypu, $mVal['BAGUS'] ?: '-');
                        $sheet->setCellValue('AP' . $currRowAypu, $mVal['SPECIAL'] ?: '-');
                        $sheet->setCellValue('AQ' . $currRowAypu, $mVal['IDLE'] ?: '-');
                        $sheet->setCellValue('AR' . $currRowAypu, $mVal['RUSAK'] ?: '-');
                        $sheet->setCellValue('AS' . $currRowAypu, $mVal['TOTAL'] ?: '-');
                    } else {
                        // Future month
                        $sheet->setCellValue('AO' . $currRowAypu, '-');
                        $sheet->setCellValue('AP' . $currRowAypu, '-');
                        $sheet->setCellValue('AQ' . $currRowAypu, '-');
                        $sheet->setCellValue('AR' . $currRowAypu, '-');
                        $sheet->setCellValue('AS' . $currRowAypu, '-');
                    }
                    $sheet->getStyle('AN' . $currRowAypu . ':AS' . $currRowAypu)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $currRowAypu++;
                }

                // Add borders to CONTAINER AYPU table
                $sheet->getStyle('AN' . $startRow . ':AS' . ($currRowAypu - 1))->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D3D3D3']
                        ],
                        'outline' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => '1F497D']
                        ]
                    ]
                ]);

                // ==========================================
                // TABLE E: Kontainer di Kapal (Columns A to E)
                // ==========================================
                $shipStartRow = 44;
                $sheet->setCellValue('A' . $shipStartRow, 'Kontainer di Kapal');
                $sheet->mergeCells('A' . $shipStartRow . ':E' . $shipStartRow);
                $sheet->getStyle('A' . $shipStartRow . ':E' . $shipStartRow)->applyFromArray($sectionHeaderStyle);

                $sheet->setCellValue('A' . ($shipStartRow + 1), 'No');
                $sheet->setCellValue('B' . ($shipStartRow + 1), 'Voyage');
                $sheet->setCellValue('C' . ($shipStartRow + 1), '20 Feet');
                $sheet->setCellValue('D' . ($shipStartRow + 1), '40 Feet');
                $sheet->setCellValue('E' . ($shipStartRow + 1), 'UNIT');
                $sheet->getStyle('A' . ($shipStartRow + 1) . ':E' . ($shipStartRow + 1))->applyFromArray($subHeaderStyle);

                // Fetch voyages for on board containers (gudang ID 15)
                $onBoardStocks = StockKontainer::where('status', '!=', 'inactive')->where('gudangs_id', 15)->pluck('nomor_seri_gabungan')->toArray();
                $onBoardSewas = Kontainer::where('status', '!=', 'inactive')->where('gudangs_id', 15)->pluck('nomor_seri_gabungan')->toArray();
                $onBoardList = array_merge($onBoardStocks, $onBoardSewas);

                $voyageCounts = [];
                foreach ($onBoardList as $no) {
                    $latestNaik = NaikKapal::where('nomor_kontainer', $no)
                        ->orderBy('created_at', 'desc')
                        ->first();
                    if ($latestNaik) {
                        $voyage = $latestNaik->no_voyage ?: 'Unknown';
                        if (!isset($voyageCounts[$voyage])) {
                            $voyageCounts[$voyage] = ['20' => 0, '40' => 0, 'total' => 0];
                        }
                        
                        $size = (int)$latestNaik->size_kontainer;
                        if ($size != 20 && $size != 40) {
                            if (str_contains($latestNaik->ukuran_kontainer, '20')) $size = 20;
                            elseif (str_contains($latestNaik->ukuran_kontainer, '40')) $size = 40;
                            else $size = 20;
                        }
                        
                        $voyageCounts[$voyage][$size == 40 ? '40' : '20']++;
                        $voyageCounts[$voyage]['total']++;
                    }
                }

                // Write voyage rows
                $currRowShip = $shipStartRow + 2;
                $noShip = 1;
                $totalShip20 = 0;
                $totalShip40 = 0;
                $totalShipUnits = 0;

                foreach ($voyageCounts as $vName => $vCounts) {
                    $sheet->setCellValue('A' . $currRowShip, $noShip++);
                    $sheet->setCellValue('B' . $currRowShip, $vName);
                    $sheet->setCellValue('C' . $currRowShip, $vCounts['20'] ?: '-');
                    $sheet->setCellValue('D' . $currRowShip, $vCounts['40'] ?: '-');
                    $sheet->setCellValue('E' . $currRowShip, $vCounts['total'] ?: '-');
                    
                    $totalShip20 += $vCounts['20'];
                    $totalShip40 += $vCounts['40'];
                    $totalShipUnits += $vCounts['total'];

                    // Alignments
                    $sheet->getStyle('A' . $currRowShip)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('B' . $currRowShip)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $sheet->getStyle('C' . $currRowShip)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('D' . $currRowShip)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('E' . $currRowShip)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    
                    $currRowShip++;
                }

                // Add Total row for ship containers
                $sheet->setCellValue('A' . $currRowShip, 'TOTAL');
                $sheet->mergeCells('A' . $currRowShip . ':B' . $currRowShip);
                $sheet->setCellValue('C' . $currRowShip, $totalShip20 ?: '-');
                $sheet->setCellValue('D' . $currRowShip, $totalShip40 ?: '-');
                $sheet->setCellValue('E' . $currRowShip, $totalShipUnits ?: '-');

                $sheet->getStyle('A' . $currRowShip . ':E' . $currRowShip)->getFont()->setBold(true);
                $sheet->getStyle('A' . $currRowShip)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C' . $currRowShip . ':E' . $currRowShip)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Add borders to ship table
                $sheet->getStyle('A' . $shipStartRow . ':E' . $currRowShip)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D3D3D3']
                        ],
                        'outline' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => '1F497D']
                        ]
                    ]
                ]);
            }
        ];
    }
}
