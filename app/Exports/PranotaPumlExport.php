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
    protected array       $rows = [];
    protected int         $dataRowCount = 0;

    public function __construct(PranotaPuml $puml)
    {
        $this->puml = $puml;
    }

    public function title(): string
    {
        return 'PUML ' . $this->puml->nomor_pranota;
    }

    public function bindValue(Cell $cell, $value)
    {
        // Pastikan kolom NIK (B) dan No REK (D) tetap sebagai string teks agar leading zeros tidak hilang
        if (in_array($cell->getColumn(), ['B', 'D']) && $cell->getRow() >= 5) {
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
        ]);

        // Build potongan map
        $potonganMap = [];
        foreach ($puml->potongans as $pot) {
            $tipe = class_basename($pot->tipe_karyawan ?: Karyawan::class);
            $key = $tipe . '_' . $pot->karyawan_id;
            $potonganMap[$key] = $pot;
        }

        // Build karyawan rekap (pertahankan urutan dari uangMakans details)
        $karyawanRekap = [];

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
                    $karyawanRekap[$kid]['hadir'] += $hadir;
                    $karyawanRekap[$kid]['total_uang_makan'] += $totalAkhir;
                    if ($rate > 0) {
                        $karyawanRekap[$kid]['nominal_per_hari'] = $rate;
                    }
                }
            }
        }

        foreach ($puml->lemburs as $lm) {
            foreach ($lm->karyawans as $d) {
                $tipe = 'Karyawan';
                $kid  = $tipe . '_' . $d->karyawan_id;
                $pot  = $potonganMap[$kid] ?? null;

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

        $periodeStr = '';
        if ($puml->periode_start && $puml->periode_end) {
            $periodeStr = 'TGL ' . $puml->periode_start->format('d/m/Y') . ' S/D ' . $puml->periode_end->format('d/m/Y');
        } elseif ($puml->tanggal_pranota) {
            $periodeStr = 'TGL ' . $puml->tanggal_pranota->format('d/m/Y');
        } else {
            $periodeStr = '-';
        }

        // ── Build rows ────────────────────────────────────────────────
        $rows = [];

        // Row 1: Blank
        $rows[] = ['', '', '', '', '', '', '', '', '', '', ''];
        // Row 2: Title
        $rows[] = ['PERINCIAN UANG MAKAN', '', '', '', '', '', '', '', '', '', ''];
        // Row 3: Periode
        $rows[] = ['PERIODE ' . $periodeStr, '', '', '', '', '', '', '', '', '', ''];
        // Row 4: Blank
        $rows[] = ['', '', '', '', '', '', '', '', '', '', ''];

        // Row 5: Header
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

        // Row 6 onwards: Data
        $no = 1;
        foreach ($karyawanRekap as $data) {
            $kar = $data['karyawan'];

            $nik = $kar ? trim((string) ($kar->nik ?? '')) : '';
            $nama = $kar ? strtoupper(trim((string) ($kar->nama_lengkap ?? ''))) : '-';
            $noRek = $kar ? trim((string) ($kar->akun_bank ?: ($kar->no_rekening ?: ''))) : '';

            $hadir = $data['hadir'] > 0 ? (int) $data['hadir'] : null;
            $rpHari = $data['nominal_per_hari'] > 0 ? (float) $data['nominal_per_hari'] : null;
            $lembur = $data['total_lembur'] > 0 ? 1 : null;
            $totalLembur = $data['total_lembur'] > 0 ? (float) $data['total_lembur'] : null;
            $totalUangMakan = $data['total_uang_makan'] > 0 ? (float) $data['total_uang_makan'] : null;
            $potTerlambat = $data['pot_terlambat'] > 0 ? (float) $data['pot_terlambat'] : null;

            $terima = (float) (
                $data['total_uang_makan']
                + $data['total_lembur']
                - $data['pot_terlambat']
                - $data['pot_utang']
                - $data['pot_bpjs']
                - $data['pot_pph']
            );

            $rows[] = [
                $no++,
                $nik,
                $nama,
                $noRek,
                $hadir,
                $rpHari,
                $lembur,
                $totalLembur,
                $totalUangMakan,
                $potTerlambat,
                $terima > 0 ? $terima : 0,
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
                $sheet   = $event->sheet->getDelegate();
                $lastRow = 5 + $this->dataRowCount;

                // Explicit column widths
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

                // Title styling
                $sheet->mergeCells("A2:K2");
                $sheet->mergeCells("A3:K3");

                $sheet->getStyle("A2")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 12, 'name' => 'Calibri'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getStyle("A3")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 11, 'name' => 'Calibri'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                // Row 5 Header styling
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
                        'startColor' => ['argb' => 'FFFFFF00'], // Bright Yellow #FFFF00
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

                // AutoFilter on row 5
                if ($this->dataRowCount > 0) {
                    $sheet->setAutoFilter("A5:K{$lastRow}");

                    // Data borders and fonts
                    $sheet->getStyle("A6:K{$lastRow}")->applyFromArray([
                        'font' => [
                            'size' => 10,
                            'name' => 'Calibri',
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color'       => ['argb' => 'FF000000'],
                            ],
                        ],
                    ]);

                    // Data row heights
                    for ($r = 6; $r <= $lastRow; $r++) {
                        $sheet->getRowDimension($r)->setRowHeight(21);
                    }

                    // Number formats & right alignment for currency: F, H, I, J, K
                    foreach (['F', 'H', 'I', 'J', 'K'] as $col) {
                        $sheet->getStyle("{$col}6:{$col}{$lastRow}")
                              ->getNumberFormat()
                              ->setFormatCode('#,##0');
                        $sheet->getStyle("{$col}6:{$col}{$lastRow}")
                              ->getAlignment()
                              ->setHorizontal(Alignment::HORIZONTAL_RIGHT)
                              ->setVertical(Alignment::VERTICAL_CENTER);
                    }

                    // Left alignment: C (NAMA), D (No REK)
                    foreach (['C', 'D'] as $col) {
                        $sheet->getStyle("{$col}6:{$col}{$lastRow}")
                              ->getAlignment()
                              ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                              ->setVertical(Alignment::VERTICAL_CENTER);
                    }

                    // Center alignment: A (NO), B (NIK), E (HADIR), G (LEMBUR)
                    foreach (['A', 'B', 'E', 'G'] as $col) {
                        $sheet->getStyle("{$col}6:{$col}{$lastRow}")
                              ->getAlignment()
                              ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                              ->setVertical(Alignment::VERTICAL_CENTER);
                    }
                }
            },
        ];
    }
}
