<?php

namespace App\Exports;

use App\Models\Karyawan;
use App\Models\PranotaPuml;
use App\Models\UangMakan;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class PranotaPumlExport extends DefaultValueBinder implements FromArray, WithTitle, WithEvents, WithCustomValueBinder
{
    protected PranotaPuml $puml;
    protected array       $rows         = [];
    protected int         $dataRowCount = 0;

    // Totals computed in array() for use in registerEvents()
    protected float $sumHadir        = 0;
    protected float $sumLemburCount  = 0;
    protected float $sumTotalLembur  = 0;
    protected float $sumTotalUm      = 0;
    protected float $sumPotTerlambat = 0;
    protected float $sumTerima       = 0;

    public function __construct(PranotaPuml $puml)
    {
        $this->puml = $puml;
    }

    public function title(): string
    {
        return 'PUML ' . $this->puml->nomor_pranota;
    }

    /**
     * Force NIK (col B) and No REK (col D) as text strings to preserve leading zeros.
     */
    public function bindValue(Cell $cell, $value)
    {
        if (in_array($cell->getColumn(), ['B', 'D']) && $cell->getRow() >= 6) {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function array(): array
    {
        $puml = $this->puml->load([
            'uangMakans.details.karyawan',
            'lemburs.karyawans.karyawan',
            'potongans',
            'creator.karyawan',
        ]);

        // ── Potongan map ──────────────────────────────────────────────
        $potonganMap = [];
        foreach ($puml->potongans as $pot) {
            $tipe = class_basename($pot->tipe_karyawan ?: Karyawan::class);
            $potonganMap[$tipe . '_' . $pot->karyawan_id] = $pot;
        }

        // ── Build karyawan rekap ──────────────────────────────────────
        $karyawanRekap = [];

        // From uang makan
        foreach ($puml->uangMakans as $um) {
            foreach ($um->details as $d) {
                $tipe = class_basename($d->tipe_karyawan ?: Karyawan::class);
                $kid  = $tipe . '_' . $d->karyawan_id;
                $pot  = $potonganMap[$kid] ?? null;

                $kar = $d->karyawan;
                if (!$kar && $d->karyawan_id) {
                    $kar = Karyawan::find($d->karyawan_id);
                }

                $hadir       = (float) ($d->kehadiran ?? 0);
                $nominalAwal = (float) ($d->nominal_awal ?? 0);
                $rate        = ($hadir > 0 && $nominalAwal > 0) ? round($nominalAwal / $hadir) : 0;
                $totalAkhir  = (float) ($d->total_akhir ?? 0);

                if ($rate == 0 && $kar && !empty($kar->nominal_uang_makan)) {
                    $rate = (float) $kar->nominal_uang_makan;
                }
                if ($rate == 0 && $d->karyawan_id) {
                    $latestUm = UangMakan::where('karyawan_id', $d->karyawan_id)->latest('tanggal')->first();
                    if ($latestUm && $latestUm->nominal > 0) {
                        $rate = (float) $latestUm->nominal;
                    }
                }
                if ($hadir == 0 && $totalAkhir > 0 && $rate > 0) {
                    $hadir = round($totalAkhir / $rate);
                }

                if (!isset($karyawanRekap[$kid])) {
                    $karyawanRekap[$kid] = [
                        'karyawan'         => $kar,
                        'hadir'            => $hadir,
                        'nominal_per_hari' => $rate,
                        'total_uang_makan' => $totalAkhir,
                        'total_lembur'     => 0,
                        'pot_terlambat'    => $pot ? (float) ($pot->pot_terlambat ?? 0) : 0,
                        'pot_utang'        => $pot ? (float) ($pot->pot_utang ?? 0) : 0,
                        'pot_bpjs'         => $pot ? (float) ($pot->pot_bpjs ?? 0) : 0,
                        'pot_pph'          => $pot ? (float) ($pot->pot_pph ?? 0) : 0,
                    ];
                } else {
                    $karyawanRekap[$kid]['hadir']            += $hadir;
                    $karyawanRekap[$kid]['total_uang_makan'] += $totalAkhir;
                    if ($rate > 0) {
                        $karyawanRekap[$kid]['nominal_per_hari'] = $rate;
                    }
                }
            }
        }

        // From lembur
        foreach ($puml->lemburs as $lm) {
            foreach ($lm->karyawans as $d) {
                $kid = 'Karyawan_' . $d->karyawan_id;
                $pot = $potonganMap[$kid] ?? null;

                $kar = $d->karyawan;
                if (!$kar && $d->karyawan_id) {
                    $kar = Karyawan::find($d->karyawan_id);
                }

                if (!isset($karyawanRekap[$kid])) {
                    $rate = ($kar && !empty($kar->nominal_uang_makan)) ? (float) $kar->nominal_uang_makan : 0;
                    if ($rate == 0 && $d->karyawan_id) {
                        $latestUm = UangMakan::where('karyawan_id', $d->karyawan_id)->latest('tanggal')->first();
                        if ($latestUm && $latestUm->nominal > 0) {
                            $rate = (float) $latestUm->nominal;
                        }
                    }

                    $karyawanRekap[$kid] = [
                        'karyawan'         => $kar,
                        'hadir'            => 0,
                        'nominal_per_hari' => $rate,
                        'total_uang_makan' => 0,
                        'total_lembur'     => (float) ($d->total_akhir ?? 0),
                        'pot_terlambat'    => $pot ? (float) ($pot->pot_terlambat ?? 0) : 0,
                        'pot_utang'        => $pot ? (float) ($pot->pot_utang ?? 0) : 0,
                        'pot_bpjs'         => $pot ? (float) ($pot->pot_bpjs ?? 0) : 0,
                        'pot_pph'          => $pot ? (float) ($pot->pot_pph ?? 0) : 0,
                    ];
                } else {
                    $karyawanRekap[$kid]['total_lembur'] += (float) ($d->total_akhir ?? 0);
                }
            }
        }

        // ── Periode string ───────────────────────────────────────────
        if ($puml->periode_start && $puml->periode_end) {
            $periodeStr = 'TGL ' . $puml->periode_start->format('d/m/Y') . ' S/D ' . $puml->periode_end->format('d/m/Y');
        } elseif ($puml->tanggal_pranota) {
            $periodeStr = 'TGL ' . $puml->tanggal_pranota->format('d/m/Y');
        } else {
            $periodeStr = '-';
        }

        // ── Creator name ──────────────────────────────────────────────
        $creatorName = '-';
        if ($puml->creator) {
            $user = $puml->creator;
            if ($user->karyawan) {
                $creatorName = strtoupper(trim($user->karyawan->nama_lengkap ?? $user->username));
            } else {
                $creatorName = strtoupper(trim($user->username ?? '-'));
            }
        }

        // ── Tanggal pranota formatted ─────────────────────────────────
        $tanggalFormatted = '';
        if ($puml->tanggal_pranota) {
            $bulanIndo = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
            ];
            $tgl = $puml->tanggal_pranota;
            $tanggalFormatted = 'JAKARTA, ' . $tgl->day . ' ' . $bulanIndo[(int)$tgl->month] . ' ' . $tgl->year;
        }

        // ── Build rows ────────────────────────────────────────────────
        $rows = [];

        // Row 1: Blank
        $rows[] = ['', '', '', '', '', '', '', '', '', '', ''];
        // Row 2: Title (merged in AfterSheet)
        $rows[] = ['PERINCIAN UANG MAKAN', '', '', '', '', '', '', '', '', '', ''];
        // Row 3: Periode (merged in AfterSheet)
        $rows[] = ['PERIODE ' . $periodeStr, '', '', '', '', '', '', '', '', '', ''];
        // Row 4: Blank
        $rows[] = ['', '', '', '', '', '', '', '', '', '', ''];

        // Row 5: Column header
        $rows[] = [
            'NO',
            'NIK',
            'NAMA',
            'No REK',
            'HADIR',
            'RP/HARI',
            'LEMBUR',
            "TOTAL\nLEMBUR",
            "TOTAL\nUANG\nMAKAN &\nLEMBUR",
            "POT\nTERLAMBAT",
            'TERIMA',
        ];

        // Rows 6+: Data
        $no = 1;
        $sumHadir = $sumLemburCount = $sumTotalLembur = $sumTotalUm = $sumPot = $sumTerima = 0.0;

        foreach ($karyawanRekap as $data) {
            $kar   = $data['karyawan'];
            $nik   = $kar ? trim((string) ($kar->nik ?? '')) : '';
            $nama  = $kar ? strtoupper(trim((string) ($kar->nama_lengkap ?? ''))) : '-';
            $noRek = $kar ? trim((string) ($kar->akun_bank ?: ($kar->no_rekening ?? ''))) : '';

            $hadir          = $data['hadir'] > 0 ? (int) $data['hadir'] : null;
            $rpHari         = $data['nominal_per_hari'] > 0 ? (float) $data['nominal_per_hari'] : null;
            $lemburFlag     = $data['total_lembur'] > 0 ? 1 : null;
            $totalLembur    = $data['total_lembur'] > 0 ? (float) $data['total_lembur'] : null;
            $totalUangMakan = $data['total_uang_makan'] > 0 ? (float) $data['total_uang_makan'] : null;
            $potTerlambat   = $data['pot_terlambat'] > 0 ? (float) $data['pot_terlambat'] : null;

            $terima = max(0, (float) (
                $data['total_uang_makan']
                + $data['total_lembur']
                - $data['pot_terlambat']
                - $data['pot_utang']
                - $data['pot_bpjs']
                - $data['pot_pph']
            ));

            // Accumulate totals
            $sumHadir        += (float) ($hadir ?? 0);
            $sumLemburCount  += (float) ($lemburFlag ?? 0);
            $sumTotalLembur  += (float) ($totalLembur ?? 0);
            $sumTotalUm      += (float) ($totalUangMakan ?? 0);
            $sumPot          += (float) ($potTerlambat ?? 0);
            $sumTerima       += $terima;

            $rows[] = [
                $no++,
                $nik,
                $nama,
                $noRek,
                $hadir,
                $rpHari,
                $lemburFlag,
                $totalLembur,
                $totalUangMakan,
                $potTerlambat,
                $terima,
            ];
        }

        // TOTAL row (row after last data)
        $rows[] = [
            '',
            '',
            'TOTAL',
            '',
            $sumHadir > 0 ? (int) $sumHadir : null,
            null,
            $sumLemburCount > 0 ? (int) $sumLemburCount : null,
            $sumTotalLembur > 0 ? $sumTotalLembur : null,
            $sumTotalUm > 0 ? $sumTotalUm : null,
            $sumPot > 0 ? $sumPot : null,
            $sumTerima,
        ];

        // 2 blank spacer rows
        $rows[] = ['', '', '', '', '', '', '', '', '', '', ''];
        $rows[] = ['', '', '', '', '', '', '', '', '', '', ''];

        // Footer: kota & tanggal, then nama pembuat
        $rows[] = ['', '', '', '', '', '', '', $tanggalFormatted, '', '', ''];
        $rows[] = ['', '', '', '', '', '', '', $creatorName, '', '', ''];

        // Store counters for AfterSheet
        $this->dataRowCount = count($karyawanRekap);
        $this->sumHadir        = $sumHadir;
        $this->sumLemburCount  = $sumLemburCount;
        $this->sumTotalLembur  = $sumTotalLembur;
        $this->sumTotalUm      = $sumTotalUm;
        $this->sumPotTerlambat = $sumPot;
        $this->sumTerima       = $sumTerima;
        $this->rows            = $rows;

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet    = $event->sheet->getDelegate();
                $dataLast = 5 + $this->dataRowCount; // last data row (row 5 = header)
                $totalRow = $dataLast + 1;            // TOTAL row
                $footerKota  = $totalRow + 3;         // 2 spacers then footer
                $footerNama  = $footerKota + 1;

                // ── Column widths ──────────────────────────────────────
                $sheet->getColumnDimension('A')->setWidth(6);
                $sheet->getColumnDimension('B')->setWidth(11);
                $sheet->getColumnDimension('C')->setWidth(34);
                $sheet->getColumnDimension('D')->setWidth(18);
                $sheet->getColumnDimension('E')->setWidth(9);
                $sheet->getColumnDimension('F')->setWidth(14);
                $sheet->getColumnDimension('G')->setWidth(11);
                $sheet->getColumnDimension('H')->setWidth(15);
                $sheet->getColumnDimension('I')->setWidth(17);
                $sheet->getColumnDimension('J')->setWidth(15);
                $sheet->getColumnDimension('K')->setWidth(16);

                // ── Title / Periode (rows 2 & 3) ──────────────────────
                $sheet->mergeCells("A2:K2");
                $sheet->mergeCells("A3:K3");

                $sheet->getStyle("A2")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 12, 'name' => 'Calibri'],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getStyle("A3")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 11, 'name' => 'Calibri'],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // ── Column header (row 5) ─────────────────────────────
                $sheet->getRowDimension(5)->setRowHeight(42);
                $sheet->getStyle("A5:K5")->applyFromArray([
                    'font' => [
                        'bold'  => true,
                        'size'  => 10,
                        'name'  => 'Calibri',
                        'color' => ['argb' => 'FF000000'],
                    ],
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFFFFF00'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'wrapText'   => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                if ($this->dataRowCount > 0) {
                    // AutoFilter hanya sampai data (exclude total row)
                    $sheet->setAutoFilter("A5:K{$dataLast}");

                    // ── Data rows (6 .. dataLast) ─────────────────────
                    $sheet->getStyle("A6:K{$dataLast}")->applyFromArray([
                        'font' => ['size' => 10, 'name' => 'Calibri'],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color'       => ['argb' => 'FF000000'],
                            ],
                        ],
                    ]);

                    for ($r = 6; $r <= $dataLast; $r++) {
                        $sheet->getRowDimension($r)->setRowHeight(21);
                    }

                    // Currency: F, H, I, J, K (data rows)
                    foreach (['F', 'H', 'I', 'J', 'K'] as $col) {
                        $sheet->getStyle("{$col}6:{$col}{$dataLast}")
                              ->getNumberFormat()->setFormatCode('#,##0');
                        $sheet->getStyle("{$col}6:{$col}{$dataLast}")
                              ->getAlignment()
                              ->setHorizontal(Alignment::HORIZONTAL_RIGHT)
                              ->setVertical(Alignment::VERTICAL_CENTER);
                    }

                    // Left: C, D
                    foreach (['C', 'D'] as $col) {
                        $sheet->getStyle("{$col}6:{$col}{$dataLast}")
                              ->getAlignment()
                              ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                              ->setVertical(Alignment::VERTICAL_CENTER);
                    }

                    // Center: A, B, E, G
                    foreach (['A', 'B', 'E', 'G'] as $col) {
                        $sheet->getStyle("{$col}6:{$col}{$dataLast}")
                              ->getAlignment()
                              ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                              ->setVertical(Alignment::VERTICAL_CENTER);
                    }

                    // ── TOTAL row ─────────────────────────────────────
                    $sheet->getRowDimension($totalRow)->setRowHeight(22);
                    $sheet->getStyle("A{$totalRow}:K{$totalRow}")->applyFromArray([
                        'font' => [
                            'bold'  => true,
                            'size'  => 10,
                            'name'  => 'Calibri',
                            'color' => ['argb' => 'FF000000'],
                        ],
                        'fill' => [
                            'fillType'   => Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'FFFFFF00'], // yellow same as header
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color'       => ['argb' => 'FF000000'],
                            ],
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical'   => Alignment::VERTICAL_CENTER,
                        ],
                    ]);

                    // Merge A-B for "TOTAL" label text in column C
                    $sheet->mergeCells("A{$totalRow}:B{$totalRow}");
                    $sheet->getStyle("C{$totalRow}")->getAlignment()
                          ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    // Currency format for TOTAL row numeric columns
                    foreach (['E', 'G', 'H', 'I', 'J', 'K'] as $col) {
                        $sheet->getStyle("{$col}{$totalRow}")
                              ->getNumberFormat()->setFormatCode('#,##0');
                        $sheet->getStyle("{$col}{$totalRow}")
                              ->getAlignment()
                              ->setHorizontal(Alignment::HORIZONTAL_RIGHT)
                              ->setVertical(Alignment::VERTICAL_CENTER);
                    }

                    // ── Footer rows ───────────────────────────────────
                    // Merge H:K for kota/tanggal and nama
                    $sheet->mergeCells("H{$footerKota}:K{$footerKota}");
                    $sheet->mergeCells("H{$footerNama}:K{$footerNama}");

                    foreach ([$footerKota, $footerNama] as $fr) {
                        $sheet->getStyle("H{$fr}")->applyFromArray([
                            'font'      => ['bold' => true, 'size' => 10, 'name' => 'Calibri'],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical'   => Alignment::VERTICAL_CENTER,
                            ],
                        ]);
                        $sheet->getRowDimension($fr)->setRowHeight(18);
                    }
                }
            },
        ];
    }
}
