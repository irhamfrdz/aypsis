<?php

namespace App\Exports;

use App\Models\Absensi;
use App\Models\Karyawan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LaporanTerlambatExport implements WithMultipleSheets
{
    use Exportable;

    protected $startDate;

    protected $endDate;

    protected $search;

    protected $pekerjaan;

    protected $divisi;

    protected $cabang;

    protected $penempatan;

    protected $grup;

    protected $subGrup;

    protected $grupBpjs;

    protected $subGrupBpjs;

    protected $statusKaryawan;

    protected $selectedKaryawan;

    protected $rekapData;

    protected $detailData;

    public function __construct(
        $startDate,
        $endDate,
        $search = null,
        $pekerjaan = null,
        $divisi = null,
        $cabang = null,
        $penempatan = null,
        $grup = null,
        $subGrup = null,
        $grupBpjs = null,
        $subGrupBpjs = null,
        $statusKaryawan = 'aktif',
        $selectedKaryawan = []
    ) {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->search = $search;
        $this->pekerjaan = $pekerjaan;
        $this->divisi = $divisi;
        $this->cabang = $cabang;
        $this->penempatan = $penempatan;
        $this->grup = $grup;
        $this->subGrup = $subGrup;
        $this->grupBpjs = $grupBpjs;
        $this->subGrupBpjs = $subGrupBpjs;
        $this->statusKaryawan = $statusKaryawan;
        $this->selectedKaryawan = $selectedKaryawan;

        $this->processData();
    }

    protected function processData()
    {
        $karyawansQuery = Karyawan::query();

        if ($this->statusKaryawan === 'aktif') {
            $karyawansQuery->whereNull('tanggal_berhenti');
        } elseif ($this->statusKaryawan === 'berhenti') {
            $karyawansQuery->whereNotNull('tanggal_berhenti');
        }

        if (! empty($this->selectedKaryawan) && is_array($this->selectedKaryawan)) {
            $karyawansQuery->whereIn('id', $this->selectedKaryawan);
        }

        if (! empty($this->search)) {
            $s = $this->search;
            $karyawansQuery->where(function ($q) use ($s) {
                $q->where('nama_lengkap', 'like', "%{$s}%")
                    ->orWhere('nama_panggilan', 'like', "%{$s}%")
                    ->orWhere('nik', 'like', "%{$s}%");
            });
        }

        if (! empty($this->penempatan)) {
            $karyawansQuery->where('penempatan', $this->penempatan);
        }

        if (! empty($this->pekerjaan)) {
            $karyawansQuery->where('pekerjaan', $this->pekerjaan);
        }

        if (! empty($this->divisi)) {
            $karyawansQuery->where('divisi', $this->divisi);
        }

        if (! empty($this->cabang)) {
            $karyawansQuery->where('cabang', $this->cabang);
        }

        if (! empty($this->grup)) {
            if (! empty($this->subGrup)) {
                $searchStr = $this->grup.':'.$this->subGrup;
                $karyawansQuery->where('grup', 'LIKE', '%"'.$searchStr.'"%');
            } else {
                $g = $this->grup;
                $karyawansQuery->where(function ($q) use ($g) {
                    $q->where('grup', 'LIKE', '%"'.$g.':%')
                        ->orWhere('grup', 'LIKE', '%"'.$g.'"%');
                });
            }
        } elseif (! empty($this->subGrup)) {
            $karyawansQuery->where('grup', 'LIKE', '%:'.$this->subGrup.'"%');
        }

        if (! empty($this->grupBpjs)) {
            if (! empty($this->subGrupBpjs)) {
                $searchStr = $this->grupBpjs.':'.$this->subGrupBpjs;
                $karyawansQuery->where('grup_bpjs', 'LIKE', '%"'.$searchStr.'"%');
            } else {
                $gb = $this->grupBpjs;
                $karyawansQuery->where(function ($q) use ($gb) {
                    $q->where('grup_bpjs', 'LIKE', '%"'.$gb.':%')
                        ->orWhere('grup_bpjs', 'LIKE', '%"'.$gb.'"%');
                });
            }
        } elseif (! empty($this->subGrupBpjs)) {
            $karyawansQuery->where('grup_bpjs', 'LIKE', '%:'.$this->subGrupBpjs.'"%');
        }

        $karyawans = $karyawansQuery->orderBy('nama_lengkap')->get();
        $karyawanIds = $karyawans->pluck('id')->toArray();

        // Approved late permissions
        $permissions = DB::table('permohonan_izins')
            ->whereIn('karyawan_id', $karyawanIds)
            ->where('status', 'APPROVED')
            ->where('jenis_izin', 'like', '%datang_terlambat%')
            ->where(function ($q) {
                $q->whereBetween('tanggal_mulai', [$this->startDate, $this->endDate])
                    ->orWhereBetween('tanggal_selesai', [$this->startDate, $this->endDate]);
            })
            ->get()
            ->groupBy('karyawan_id');

        // Fetch attendance logs within date buffer (+/- 1 day)
        $startObj = Carbon::parse($this->startDate)->startOfDay()->subDay()->setTime(6, 0, 0);
        $endObj = Carbon::parse($this->endDate)->endOfDay()->addDay()->setTime(5, 59, 59);

        $rawLogs = Absensi::whereIn('karyawan_id', $karyawanIds)
            ->whereBetween('waktu', [$startObj, $endObj])
            ->orderBy('waktu')
            ->get();

        $logsByKaryawan = $rawLogs->groupBy('karyawan_id');

        $detailRows = [];
        $summaryRows = [];

        foreach ($karyawans as $karyawan) {
            if ($karyawan->isExemptFromTerlambat()) {
                continue;
            }

            $empLogs = $logsByKaryawan->get($karyawan->id, collect());
            $empPerms = $permissions->get($karyawan->id, collect());

            $empLogsByDate = $empLogs->groupBy(function ($log) {
                return Carbon::parse($log->waktu)->subHours(6)->toDateString();
            });

            $totalKali = 0;
            $totalMenit = 0;

            $tempDate = Carbon::parse($this->startDate);
            $lastDate = Carbon::parse($this->endDate);

            while ($tempDate->lte($lastDate)) {
                $dateStr = $tempDate->toDateString();
                $dayLogs = $empLogsByDate->get($dateStr, collect());

                $masukLog = $dayLogs->first(function ($val) {
                    $t = strtolower(trim(str_replace('_', ' ', $val->tipe ?? '')));

                    return in_array($t, ['masuk', 'check in', 'in']);
                });

                if ($masukLog) {
                    $waktuMasuk = Carbon::parse($masukLog->waktu);
                    $jamMasukNormal = Carbon::parse($dateStr.' 09:00:00');
                    $batasToleransi = $jamMasukNormal->copy()->addMinutes(5);

                    if ($waktuMasuk->gt($batasToleransi)) {
                        $hasPerm = $empPerms->contains(function ($perm) use ($dateStr) {
                            return $dateStr >= $perm->tanggal_mulai && $dateStr <= $perm->tanggal_selesai;
                        });

                        if (! $hasPerm) {
                            $menit = $jamMasukNormal->diffInMinutes($waktuMasuk);
                            $totalKali++;
                            $totalMenit += $menit;

                            $detailRows[] = [
                                'tanggal_raw' => $dateStr,
                                'tanggal' => $tempDate->translatedFormat('d M Y'),
                                'hari' => $tempDate->translatedFormat('l'),
                                'nik' => $karyawan->nik,
                                'nama' => $karyawan->nama_lengkap,
                                'penempatan' => $karyawan->penempatan ?: '-',
                                'divisi' => $karyawan->divisi ?: '-',
                                'pekerjaan' => $karyawan->pekerjaan ?: '-',
                                'jam_standar' => $jamMasukNormal->format('H:i'),
                                'jam_masuk' => $waktuMasuk->format('H:i:s'),
                                'menit_terlambat' => $menit,
                                'keterangan' => 'Terlambat '.$menit.' Menit',
                            ];
                        }
                    }
                }

                $tempDate->addDay();
            }

            if ($totalKali > 0) {
                $summaryRows[] = [
                    'nik' => $karyawan->nik,
                    'nama' => $karyawan->nama_lengkap,
                    'penempatan' => $karyawan->penempatan ?: '-',
                    'divisi' => $karyawan->divisi ?: '-',
                    'pekerjaan' => $karyawan->pekerjaan ?: '-',
                    'total_kali' => $totalKali,
                    'total_menit' => $totalMenit,
                    'rata_rata_menit' => round($totalMenit / $totalKali, 1),
                ];
            }
        }

        // Sort summary: most frequent late first, then most minutes
        usort($summaryRows, function ($a, $b) {
            if ($a['total_kali'] === $b['total_kali']) {
                return $b['total_menit'] <=> $a['total_menit'];
            }

            return $b['total_kali'] <=> $a['total_kali'];
        });

        // Sort detail: by date ascending, then name ascending
        usort($detailRows, function ($a, $b) {
            if ($a['tanggal_raw'] === $b['tanggal_raw']) {
                return strcmp($a['nama'], $b['nama']);
            }

            return strcmp($a['tanggal_raw'], $b['tanggal_raw']);
        });

        $this->rekapData = collect($summaryRows);
        $this->detailData = collect($detailRows);
    }

    public function sheets(): array
    {
        $meta = [
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'penempatan' => $this->penempatan,
        ];

        return [
            new TerlambatRekapSheet($this->rekapData, $meta),
            new TerlambatDetailSheet($this->detailData, $meta),
        ];
    }
}

/**
 * Sheet 1: Rekap Keterlambatan Per Karyawan
 */
class TerlambatRekapSheet implements FromCollection, ShouldAutoSize, WithEvents, WithTitle
{
    protected $data;

    protected $meta;

    public function __construct($data, $meta)
    {
        $this->data = $data;
        $this->meta = $meta;
    }

    public function collection()
    {
        $rows = [];
        $no = 1;
        foreach ($this->data as $item) {
            $rows[] = [
                $no++,
                $item['nik'],
                $item['nama'],
                $item['penempatan'],
                $item['divisi'],
                $item['pekerjaan'],
                $item['total_kali'],
                $item['total_menit'],
                $item['rata_rata_menit'],
            ];
        }

        return collect($rows);
    }

    public function title(): string
    {
        return 'Rekap Keterlambatan';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->setShowGridlines(true);

                // Header Titles
                $sheet->setCellValue('A2', 'REKAPITULASI KETERLAMBATAN KARYAWAN');
                $sheet->mergeCells('A2:I2');
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['name' => 'Calibri', 'size' => 14, 'bold' => true, 'color' => ['rgb' => '1E293B']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $periodeText = 'Periode: '.Carbon::parse($this->meta['startDate'])->translatedFormat('d M Y').' s/d '.Carbon::parse($this->meta['endDate'])->translatedFormat('d M Y');
                if (! empty($this->meta['penempatan'])) {
                    $periodeText .= ' | Penempatan: '.strtoupper($this->meta['penempatan']);
                }

                $sheet->setCellValue('A3', $periodeText);
                $sheet->mergeCells('A3:I3');
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['name' => 'Calibri', 'size' => 11, 'color' => ['rgb' => '64748B']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                // Table Headings on Row 5
                $headers = [
                    'A5' => 'No',
                    'B5' => 'NIK',
                    'C5' => 'Nama Lengkap',
                    'D5' => 'Penempatan',
                    'E5' => 'Divisi',
                    'F5' => 'Pekerjaan',
                    'G5' => 'Frekuensi (Kali)',
                    'H5' => 'Total Terlambat (Menit)',
                    'I5' => 'Rata-rata (Menit)',
                ];

                foreach ($headers as $cell => $text) {
                    $sheet->setCellValue($cell, $text);
                }

                $headerStyle = [
                    'font' => ['name' => 'Calibri', 'size' => 11, 'bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EA580C']], // Orange header
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'C2410C']]],
                ];
                $sheet->getStyle('A5:I5')->applyFromArray($headerStyle);
                $sheet->getRowDimension(5)->setRowHeight(28);

                $rowCount = count($this->data);
                $startRow = 6;
                $endRow = $startRow + $rowCount - 1;

                if ($rowCount > 0) {
                    // Body styling
                    $sheet->getStyle("A{$startRow}:I{$endRow}")->applyFromArray([
                        'font' => ['name' => 'Calibri', 'size' => 10],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
                    ]);

                    $sheet->getStyle("A{$startRow}:A{$endRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("B{$startRow}:B{$endRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("G{$startRow}:I{$endRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                    // Total Row
                    $totalRow = $endRow + 1;
                    $sheet->setCellValue("A{$totalRow}", 'TOTAL');
                    $sheet->mergeCells("A{$totalRow}:F{$totalRow}");
                    $sheet->setCellValue("G{$totalRow}", "=SUM(G{$startRow}:G{$endRow})");
                    $sheet->setCellValue("H{$totalRow}", "=SUM(H{$startRow}:H{$endRow})");
                    $sheet->setCellValue("I{$totalRow}", "=AVERAGE(I{$startRow}:I{$endRow})");

                    $totalStyle = [
                        'font' => ['name' => 'Calibri', 'size' => 10, 'bold' => true],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '94A3B8']]],
                    ];
                    $sheet->getStyle("A{$totalRow}:I{$totalRow}")->applyFromArray($totalStyle);
                    $sheet->getStyle("A{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("G{$totalRow}:I{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                } else {
                    $sheet->setCellValue('A6', 'Tidak ada data keterlambatan pada periode ini.');
                    $sheet->mergeCells('A6:I6');
                    $sheet->getStyle('A6')->applyFromArray([
                        'font' => ['name' => 'Calibri', 'size' => 11, 'italic' => true, 'color' => ['rgb' => '64748B']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                }
            },
        ];
    }
}

/**
 * Sheet 2: Detail Kejadian Terlambat
 */
class TerlambatDetailSheet implements FromCollection, ShouldAutoSize, WithEvents, WithTitle
{
    protected $data;

    protected $meta;

    public function __construct($data, $meta)
    {
        $this->data = $data;
        $this->meta = $meta;
    }

    public function collection()
    {
        $rows = [];
        $no = 1;
        foreach ($this->data as $item) {
            $rows[] = [
                $no++,
                $item['tanggal'],
                $item['hari'],
                $item['nik'],
                $item['nama'],
                $item['penempatan'],
                $item['divisi'],
                $item['pekerjaan'],
                $item['jam_standar'],
                $item['jam_masuk'],
                $item['menit_terlambat'],
                $item['keterangan'],
            ];
        }

        return collect($rows);
    }

    public function title(): string
    {
        return 'Rincian Log Terlambat';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->setShowGridlines(true);

                // Header Titles
                $sheet->setCellValue('A2', 'RINCIAN LOG KETERLAMBATAN KARYAWAN');
                $sheet->mergeCells('A2:L2');
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['name' => 'Calibri', 'size' => 14, 'bold' => true, 'color' => ['rgb' => '1E293B']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $periodeText = 'Periode: '.Carbon::parse($this->meta['startDate'])->translatedFormat('d M Y').' s/d '.Carbon::parse($this->meta['endDate'])->translatedFormat('d M Y');
                if (! empty($this->meta['penempatan'])) {
                    $periodeText .= ' | Penempatan: '.strtoupper($this->meta['penempatan']);
                }

                $sheet->setCellValue('A3', $periodeText);
                $sheet->mergeCells('A3:L3');
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['name' => 'Calibri', 'size' => 11, 'color' => ['rgb' => '64748B']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                // Table Headings on Row 5
                $headers = [
                    'A5' => 'No',
                    'B5' => 'Tanggal',
                    'C5' => 'Hari',
                    'D5' => 'NIK',
                    'E5' => 'Nama Lengkap',
                    'F5' => 'Penempatan',
                    'G5' => 'Divisi',
                    'H5' => 'Pekerjaan',
                    'I5' => 'Jam Standar',
                    'J5' => 'Jam Masuk',
                    'K5' => 'Terlambat (Menit)',
                    'L5' => 'Keterangan',
                ];

                foreach ($headers as $cell => $text) {
                    $sheet->setCellValue($cell, $text);
                }

                $headerStyle = [
                    'font' => ['name' => 'Calibri', 'size' => 11, 'bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'C2410C']], // Darker Orange
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9A3412']]],
                ];
                $sheet->getStyle('A5:L5')->applyFromArray($headerStyle);
                $sheet->getRowDimension(5)->setRowHeight(28);

                $rowCount = count($this->data);
                $startRow = 6;
                $endRow = $startRow + $rowCount - 1;

                if ($rowCount > 0) {
                    $sheet->getStyle("A{$startRow}:L{$endRow}")->applyFromArray([
                        'font' => ['name' => 'Calibri', 'size' => 10],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
                    ]);

                    $sheet->getStyle("A{$startRow}:A{$endRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("B{$startRow}:D{$endRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("I{$startRow}:J{$endRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("K{$startRow}:K{$endRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                } else {
                    $sheet->setCellValue('A6', 'Tidak ada data rincian keterlambatan pada periode ini.');
                    $sheet->mergeCells('A6:L6');
                    $sheet->getStyle('A6')->applyFromArray([
                        'font' => ['name' => 'Calibri', 'size' => 11, 'italic' => true, 'color' => ['rgb' => '64748B']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                }
            },
        ];
    }
}
