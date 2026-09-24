<?php

namespace App\Exports;

use App\Models\PranotaPuml;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PranotaPumlExport implements FromArray, WithTitle, ShouldAutoSize, WithEvents
{
    protected PranotaPuml $puml;
    protected array       $rows = [];
    protected int         $dataRowCount = 0;

    // Kolom terakhir (K = 11 kolom)
    protected string $lastCol = 'K';

    public function __construct(PranotaPuml $puml)
    {
        $this->puml = $puml;
    }

    public function title(): string
    {
        return 'PUML ' . $this->puml->nomor_pranota;
    }

    public function array(): array
    {
        $puml = $this->puml->load([
            'uangMakans.details.karyawan',
            'lemburs.karyawans.karyawan',
            'potongans',
        ]);

        // Build potongan map
        $potonganMap = [];
        foreach ($puml->potongans as $pot) {
            $key = class_basename($pot->tipe_karyawan) . '_' . $pot->karyawan_id;
            $potonganMap[$key] = $pot;
        }

        // Build karyawan rekap
        $karyawanRekap = [];

        foreach ($puml->uangMakans as $um) {
            foreach ($um->details as $d) {
                $kid = class_basename($d->tipe_karyawan) . '_' . $d->karyawan_id;
                if (!isset($karyawanRekap[$kid])) {
                    $pot = $potonganMap[$kid] ?? null;
                    $karyawanRekap[$kid] = [
                        'karyawan'        => $d->karyawan,
                        'hadir'           => $d->total_hadir ?? 0,
                        'nominal_per_hari'=> $d->nominal_per_hari ?? 0,
                        'total_uang_makan'=> 0,
                        'total_lembur'    => 0,
                        'pot_terlambat'   => $pot ? ($pot->pot_terlambat ?? 0) : 0,
                        'pot_utang'       => $pot ? ($pot->pot_utang ?? 0) : 0,
                        'pot_bpjs'        => $pot ? ($pot->pot_bpjs ?? 0) : 0,
                        'pot_pph'         => $pot ? ($pot->pot_pph ?? 0) : 0,
                    ];
                }
                $karyawanRekap[$kid]['total_uang_makan'] += $d->total_akhir ?? 0;
            }
        }

        foreach ($puml->lemburs as $lm) {
            foreach ($lm->karyawans as $d) {
                $tipe = $d->tipe_karyawan ?? 'App\\Models\\Karyawan';
                $kid  = class_basename($tipe) . '_' . $d->karyawan_id;
                if (!isset($karyawanRekap[$kid])) {
                    $pot = $potonganMap[$kid] ?? null;
                    $karyawanRekap[$kid] = [
                        'karyawan'        => $d->karyawan,
                        'hadir'           => 0,
                        'nominal_per_hari'=> 0,
                        'total_uang_makan'=> 0,
                        'total_lembur'    => 0,
                        'pot_terlambat'   => $pot ? ($pot->pot_terlambat ?? 0) : 0,
                        'pot_utang'       => $pot ? ($pot->pot_utang ?? 0) : 0,
                        'pot_bpjs'        => $pot ? ($pot->pot_bpjs ?? 0) : 0,
                        'pot_pph'         => $pot ? ($pot->pot_pph ?? 0) : 0,
                    ];
                }
                $karyawanRekap[$kid]['total_lembur'] += $d->total_akhir ?? 0;
            }
        }

        // Sort by nama
        uasort($karyawanRekap, fn($a, $b) =>
            strtolower($a['karyawan']->nama_lengkap ?? 'z') <=>
            strtolower($b['karyawan']->nama_lengkap ?? 'z')
        );

        $periodeStr = '';
        if ($puml->periode_start && $puml->periode_end) {
            $periodeStr = $puml->periode_start->format('d/m/Y') . ' S/D ' . $puml->periode_end->format('d/m/Y');
        } elseif ($puml->tanggal_pranota) {
            $periodeStr = 'TGL ' . $puml->tanggal_pranota->format('d/m/Y');
        }

        // ── Build rows ────────────────────────────────────────────────
        $rows = [];

        // Title
        $rows[] = ['PERINCIAN UANG MAKAN', '', '', '', '', '', '', '', '', '', ''];
        $rows[] = ['PERIODE ' . $periodeStr,    '', '', '', '', '', '', '', '', '', ''];
        $rows[] = []; // Spacer

        // Header
        $rows[] = [
            'NO', 'NIK', 'NAMA', 'No Rek',
            'HADIR', 'RP/HARI', 'LEMBUR',
            'TOTAL LEMBUR', 'TOTAL UANG MAKAN & LEMBUR',
            'POT TERLAMBAT', 'TERIMA',
        ];

        // Data
        $no = 1;
        foreach ($karyawanRekap as $data) {
            $kar   = $data['karyawan'];
            $total = $data['total_uang_makan'] + $data['total_lembur'];
            $pot   = $data['pot_terlambat'] + $data['pot_utang'] + $data['pot_bpjs'] + $data['pot_pph'];
            $terima = $total - $pot;

            $rows[] = [
                $no++,
                $kar->nik ?? '-',
                $kar->nama_lengkap ?? '-',
                $kar->no_rekening ?? '-',
                $data['hadir'],
                $data['nominal_per_hari'],
                $data['total_lembur'] > 0 ? 1 : 0,   // flag lembur (ada/tidak)
                $data['total_lembur'],
                $total,
                $data['pot_terlambat'],
                $terima,
            ];
        }

        $this->dataRowCount = count($karyawanRekap);
        $this->rows = $rows;

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet      = $event->sheet->getDelegate();
                $totalRows  = 4 + $this->dataRowCount; // 3 header rows + 1 col header + data
                $lastCol    = $this->lastCol;

                // ── Merge Title ──────────────────────────────────────
                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->mergeCells("A2:{$lastCol}2");

                $sheet->getStyle("A1:A2")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 13],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // ── Column Header (Row 4) — Yellow like the image ────
                $sheet->getStyle("A4:{$lastCol}4")->applyFromArray([
                    'font' => [
                        'bold'  => true,
                        'color' => ['argb' => 'FF000000'],
                    ],
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFFFFF00'], // Yellow
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

                // ── Data Borders ─────────────────────────────────────
                if ($this->dataRowCount > 0) {
                    $sheet->getStyle("A5:{$lastCol}{$totalRows}")->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color'       => ['argb' => 'FF000000'],
                            ],
                        ],
                    ]);

                    // Number format for currency columns
                    foreach (['F', 'H', 'I', 'J', 'K'] as $col) {
                        $sheet->getStyle("{$col}5:{$col}{$totalRows}")
                              ->getNumberFormat()
                              ->setFormatCode('#,##0');
                    }

                    // Center: NO, NIK, HADIR, LEMBUR flag
                    foreach (['A', 'B', 'E', 'G'] as $col) {
                        $sheet->getStyle("{$col}5:{$col}{$totalRows}")
                              ->getAlignment()
                              ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }

                    // Alternate row shading (light yellow)
                    for ($r = 5; $r <= $totalRows; $r++) {
                        if ($r % 2 === 0) {
                            $sheet->getStyle("A{$r}:{$lastCol}{$r}")->applyFromArray([
                                'fill' => [
                                    'fillType'   => Fill::FILL_SOLID,
                                    'startColor' => ['argb' => 'FFFFFDE7'],
                                ],
                            ]);
                        }
                    }
                }

                // Row 4 height
                $sheet->getRowDimension(4)->setRowHeight(36);
            },
        ];
    }
}
