<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LaporanIjinKaryawanExport implements WithColumnWidths, WithEvents, WithTitle
{
    protected $startDate;

    protected $endDate;

    protected $search;

    protected $pekerjaan;

    protected $divisi;

    protected $penempatan;

    protected $statusKaryawan;

    public function __construct($startDate, $endDate, $search = null, $pekerjaan = null, $divisi = null, $penempatan = null, $statusKaryawan = 'aktif')
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->search = $search;
        $this->pekerjaan = $pekerjaan;
        $this->divisi = $divisi;
        $this->penempatan = $penempatan;
        $this->statusKaryawan = $statusKaryawan;
    }

    public function title(): string
    {
        return 'Laporan Ijin Karyawan';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,
            'B' => 32,
            'C' => 20,
            'D' => 14,
            'E' => 10,
            'F' => 10,
            'G' => 10,
            'H' => 38,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $start = Carbon::parse($this->startDate)->toDateString();
                $end = Carbon::parse($this->endDate)->toDateString();

                // 1. Fetch Permohonan Izin
                $izinsQuery = DB::table('permohonan_izins')
                    ->leftJoin('karyawans', function ($join) {
                        $join->on('permohonan_izins.karyawan_id', '=', 'karyawans.id')
                            ->orOn('permohonan_izins.nik', '=', 'karyawans.nik');
                    })
                    ->where(function ($q) {
                        $q->whereIn('permohonan_izins.status', ['APPROVED', 'Disetujui', 'approved'])
                            ->orWhereNull('permohonan_izins.status');
                    })
                    ->where(function ($q) use ($start, $end) {
                        $q->whereBetween('permohonan_izins.tanggal_mulai', [$start, $end])
                            ->orWhereBetween('permohonan_izins.tanggal_selesai', [$start, $end])
                            ->orWhere(function ($sub) use ($start, $end) {
                                $sub->where('permohonan_izins.tanggal_mulai', '<=', $start)
                                    ->where('permohonan_izins.tanggal_selesai', '>=', $end);
                            });
                    })
                    ->select(
                        'permohonan_izins.id',
                        'permohonan_izins.karyawan_id',
                        DB::raw('COALESCE(karyawans.nik, permohonan_izins.nik) as nik'),
                        DB::raw('COALESCE(karyawans.nama_lengkap, permohonan_izins.nama) as nama_lengkap'),
                        'karyawans.divisi as kar_divisi',
                        'karyawans.pekerjaan as kar_pekerjaan',
                        'permohonan_izins.divisi as izin_divisi',
                        'permohonan_izins.jenis_izin',
                        'permohonan_izins.tanggal_mulai',
                        'permohonan_izins.tanggal_selesai',
                        'permohonan_izins.waktu',
                        'permohonan_izins.alasan'
                    );

                // 2. Fetch Cuti
                $cutisQuery = DB::table('cutis')
                    ->leftJoin('karyawans', 'cutis.karyawan_id', '=', 'karyawans.id')
                    ->whereIn('cutis.status', ['APPROVED', 'Disetujui', 'approved'])
                    ->where(function ($q) use ($start, $end) {
                        $q->whereBetween('cutis.tanggal_mulai', [$start, $end])
                            ->orWhereBetween('cutis.tanggal_selesai', [$start, $end])
                            ->orWhere(function ($sub) use ($start, $end) {
                                $sub->where('cutis.tanggal_mulai', '<=', $start)
                                    ->where('cutis.tanggal_selesai', '>=', $end);
                            });
                    })
                    ->select(
                        'cutis.id',
                        'cutis.karyawan_id',
                        'karyawans.nik',
                        'karyawans.nama_lengkap',
                        'karyawans.divisi as kar_divisi',
                        'karyawans.pekerjaan as kar_pekerjaan',
                        DB::raw('NULL as izin_divisi'),
                        DB::raw("CONCAT('Cuti ', cutis.jenis_cuti) as jenis_izin"),
                        'cutis.tanggal_mulai',
                        'cutis.tanggal_selesai',
                        DB::raw('NULL as waktu'),
                        'cutis.keterangan as alasan'
                    );

                // Apply Filters
                if (! empty($this->search)) {
                    $s = $this->search;
                    $izinsQuery->where(function ($q) use ($s) {
                        $q->where('karyawans.nama_lengkap', 'like', "%{$s}%")
                            ->orWhere('permohonan_izins.nama', 'like', "%{$s}%")
                            ->orWhere('permohonan_izins.nik', 'like', "%{$s}%");
                    });
                    $cutisQuery->where(function ($q) use ($s) {
                        $q->where('karyawans.nama_lengkap', 'like', "%{$s}%")
                            ->orWhere('karyawans.nik', 'like', "%{$s}%");
                    });
                }

                if (! empty($this->pekerjaan)) {
                    $izinsQuery->where('karyawans.pekerjaan', $this->pekerjaan);
                    $cutisQuery->where('karyawans.pekerjaan', $this->pekerjaan);
                }

                if (! empty($this->divisi)) {
                    $izinsQuery->where(function ($q) {
                        $q->where('karyawans.divisi', $this->divisi)
                            ->orWhere('permohonan_izins.divisi', $this->divisi);
                    });
                    $cutisQuery->where('karyawans.divisi', $this->divisi);
                }

                if (! empty($this->penempatan)) {
                    $izinsQuery->where('karyawans.penempatan', $this->penempatan);
                    $cutisQuery->where('karyawans.penempatan', $this->penempatan);
                }

                if ($this->statusKaryawan === 'aktif') {
                    $izinsQuery->whereNull('karyawans.tanggal_berhenti');
                    $cutisQuery->whereNull('karyawans.tanggal_berhenti');
                } elseif ($this->statusKaryawan === 'berhenti') {
                    $izinsQuery->whereNotNull('karyawans.tanggal_berhenti');
                    $cutisQuery->whereNotNull('karyawans.tanggal_berhenti');
                }

                $izins = $izinsQuery->get();
                $cutis = $cutisQuery->get();

                $rows = collect();

                // Process Izins
                foreach ($izins as $izin) {
                    $parsedWaktu = $this->parseWaktu($izin->waktu);
                    $bagian = $this->formatBagian($izin->kar_divisi ?: $izin->izin_divisi, $izin->kar_pekerjaan);

                    $curDate = Carbon::parse(max($izin->tanggal_mulai, $start));
                    $lastDate = Carbon::parse(min($izin->tanggal_selesai, $end));

                    while ($curDate->lte($lastDate)) {
                        if (! $curDate->isSunday()) {
                            $rows->push([
                                'nik' => (string) ($izin->nik ?? '-'),
                                'nama' => strtoupper($izin->nama_lengkap ?? '-'),
                                'bagian' => $bagian,
                                'tgl_raw' => $curDate->copy(),
                                'tgl_ijin' => $curDate->format('d-M-y'),
                                'dari' => $parsedWaktu['dari'],
                                'sampai' => $parsedWaktu['sampai'],
                                'keterangan' => $izin->alasan ?: ($izin->jenis_izin ?: 'Izin'),
                            ]);
                        }
                        $curDate->addDay();
                    }
                }

                // Process Cutis
                foreach ($cutis as $cuti) {
                    $bagian = $this->formatBagian($cuti->kar_divisi, $cuti->kar_pekerjaan);

                    $curDate = Carbon::parse(max($cuti->tanggal_mulai, $start));
                    $lastDate = Carbon::parse(min($cuti->tanggal_selesai, $end));

                    while ($curDate->lte($lastDate)) {
                        if (! $curDate->isSunday()) {
                            $rows->push([
                                'nik' => (string) ($cuti->nik ?? '-'),
                                'nama' => strtoupper($cuti->nama_lengkap ?? '-'),
                                'bagian' => $bagian,
                                'tgl_raw' => $curDate->copy(),
                                'tgl_ijin' => $curDate->format('d-M-y'),
                                'dari' => null,
                                'sampai' => null,
                                'keterangan' => $cuti->alasan ?: ($cuti->jenis_izin ?: 'Cuti'),
                            ]);
                        }
                        $curDate->addDay();
                    }
                }

                // Sort chronologically by date, then NIK
                $sortedRows = $rows->sortBy([
                    fn ($a, $b) => $a['tgl_raw']->timestamp <=> $b['tgl_raw']->timestamp,
                    fn ($a, $b) => strcmp($a['nik'], $b['nik']),
                ])->values();

                // 3. Build Spreadsheet Layout
                // Set Sheet Views / Gridlines
                $sheet->setShowGridlines(true);

                // Title on Row 2
                $sheet->setCellValue('A2', 'Laporan Ijin  Karyawan');
                $sheet->mergeCells('A2:H2');
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => [
                        'name' => 'Calibri',
                        'size' => 14,
                        'bold' => true,
                        'color' => ['argb' => 'FF000000'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(24);

                // Table Header on Row 4
                $headers = [
                    'A4' => 'NO NIK',
                    'B4' => 'NAMA',
                    'C4' => 'BAGIAN',
                    'D4' => 'TGL IJIN',
                    'E4' => 'DARI',
                    'F4' => 'SAMPAI',
                    'G4' => 'LAMA',
                    'H4' => 'KETERANGAN',
                ];

                foreach ($headers as $cell => $text) {
                    $sheet->setCellValue($cell, $text);
                }

                $sheet->getStyle('A4:H4')->applyFromArray([
                    'font' => [
                        'name' => 'Calibri',
                        'size' => 11,
                        'bold' => true,
                        'color' => ['argb' => 'FF000000'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFFF0000'], // Solid Red
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getRowDimension(4)->setRowHeight(26);

                // Data Rows starting from Row 5
                $rowNum = 5;
                foreach ($sortedRows as $row) {
                    // NO NIK as explicit string so leading 0 isn't dropped
                    $sheet->getCell("A{$rowNum}")->setValueExplicit($row['nik'], DataType::TYPE_STRING);
                    $sheet->setCellValue("B{$rowNum}", $row['nama']);
                    $sheet->setCellValue("C{$rowNum}", $row['bagian']);
                    $sheet->setCellValue("D{$rowNum}", $row['tgl_ijin']);

                    if ($row['dari'] !== null) {
                        $sheet->setCellValue("E{$rowNum}", $row['dari']);
                    }
                    if ($row['sampai'] !== null) {
                        $sheet->setCellValue("F{$rowNum}", $row['sampai']);
                    }

                    // Formula for LAMA: =F{row}-E{row}
                    $sheet->setCellValue("G{$rowNum}", "=F{$rowNum}-E{$rowNum}");

                    $sheet->setCellValue("H{$rowNum}", $row['keterangan']);

                    $sheet->getRowDimension($rowNum)->setRowHeight(20);
                    $rowNum++;
                }

                // Add 15 extra empty rows with formula & borders (like template in screenshot)
                $extraCount = 15;
                for ($k = 0; $k < $extraCount; $k++) {
                    $sheet->setCellValue("G{$rowNum}", "=F{$rowNum}-E{$rowNum}");
                    $sheet->getRowDimension($rowNum)->setRowHeight(20);
                    $rowNum++;
                }

                $lastRow = $rowNum - 1;

                // Apply Table Styles (Row 4 to lastRow)
                $sheet->getStyle("A4:H{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                // Alignments
                $sheet->getStyle("A5:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("B5:C{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle("D5:D{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("E5:G{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("H5:H{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                // Number formatting for DARI, SAMPAI, and LAMA: 0.00
                $sheet->getStyle("E5:G{$lastRow}")->getNumberFormat()->setFormatCode('#,##0.00;[Red]-#,##0.00;0.00');
            },
        ];
    }

    /**
     * Parse waktu string like "09:00 - 13:00", "09:00", "9.52", etc.
     */
    protected function parseWaktu(?string $waktuStr): array
    {
        if (empty($waktuStr)) {
            return ['dari' => null, 'sampai' => null];
        }

        $clean = trim($waktuStr);

        // If contains range separator (-, s/d, sd, to)
        if (preg_match('/^(.*?)(?:\s*(?:-|s\/d|sd|to)\s*)(.*)$/i', $clean, $matches)) {
            return [
                'dari' => $this->timeToFloat($matches[1]),
                'sampai' => $this->timeToFloat($matches[2]),
            ];
        }

        // Single time
        return [
            'dari' => $this->timeToFloat($clean),
            'sampai' => null,
        ];
    }

    /**
     * Convert time like "09:00", "9:52", "13:00" to float like 9.00, 9.52, 13.00
     */
    protected function timeToFloat(?string $timeStr): ?float
    {
        if (empty($timeStr)) {
            return null;
        }

        $timeStr = trim($timeStr);

        if (preg_match('/(\d{1,2})[:.](\d{2})/', $timeStr, $m)) {
            return (float) ($m[1].'.'.$m[2]);
        }

        if (is_numeric($timeStr)) {
            return (float) $timeStr;
        }

        return null;
    }

    /**
     * Format bagian based on divisi and pekerjaan
     */
    protected function formatBagian(?string $divisi, ?string $pekerjaan): string
    {
        $pekerjaan = strtoupper(trim($pekerjaan ?? ''));
        $divisi = strtoupper(trim($divisi ?? ''));

        if ($pekerjaan === 'SEKRETARIS' || $pekerjaan === 'MARKETING') {
            return $pekerjaan;
        }

        if (str_contains($divisi, 'ADMINISTRASI') || str_contains($divisi, 'ADM')) {
            if ($pekerjaan !== '' && ! str_contains($pekerjaan, 'ADMINISTRASI') && ! str_contains($pekerjaan, 'ADM')) {
                return 'ADM. '.$pekerjaan;
            }

            return 'ADM';
        }

        if ($pekerjaan !== '') {
            return $pekerjaan;
        }

        return $divisi ?: '-';
    }
}
