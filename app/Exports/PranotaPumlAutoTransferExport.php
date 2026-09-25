<?php

namespace App\Exports;

use App\Models\Karyawan;
use App\Models\PranotaPuml;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PranotaPumlAutoTransferExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting, ShouldAutoSize, WithStyles
{
    protected PranotaPuml $puml;
    protected int $rowNumber = 1;
    protected int $dataCount = 0;

    public function __construct(PranotaPuml $puml)
    {
        $this->puml = $puml;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $puml = $this->puml->load([
            'uangMakans.details.karyawan',
            'lemburs.karyawans.karyawan',
            'potongans',
        ]);

        $potonganMap = [];
        foreach ($puml->potongans as $pot) {
            $tipe = class_basename($pot->tipe_karyawan ?: Karyawan::class);
            $potonganMap[$tipe . '_' . $pot->karyawan_id] = $pot;
        }

        $karyawanRekap = [];

        // Rekap dari Uang Makan
        foreach ($puml->uangMakans as $um) {
            foreach ($um->details as $d) {
                $tipe = class_basename($d->tipe_karyawan ?: Karyawan::class);
                $kid = $tipe . '_' . $d->karyawan_id;
                $pot = $potonganMap[$kid] ?? null;

                $kar = $d->karyawan;
                if (!$kar && $d->karyawan_id) {
                    $kar = Karyawan::find($d->karyawan_id);
                }

                if (!isset($karyawanRekap[$kid])) {
                    $karyawanRekap[$kid] = [
                        'karyawan'         => $kar,
                        'total_uang_makan' => 0,
                        'total_lembur'     => 0,
                        'pot_utang'        => $pot ? (float) ($pot->pot_utang ?? 0) : 0,
                        'pot_bpjs'         => $pot ? (float) ($pot->pot_bpjs ?? 0) : 0,
                        'pot_pph'          => $pot ? (float) ($pot->pot_pph ?? 0) : 0,
                        'pot_terlambat'    => $pot ? (float) ($pot->pot_terlambat ?? 0) : 0,
                    ];
                }
                $karyawanRekap[$kid]['total_uang_makan'] += (float) ($d->total_akhir ?? 0);
            }
        }

        // Rekap dari Lembur
        foreach ($puml->lemburs as $lm) {
            foreach ($lm->karyawans as $d) {
                $tipe_karyawan = $d->tipe_karyawan ?? 'App\\Models\\Karyawan';
                $tipe = class_basename($tipe_karyawan);
                $kid = $tipe . '_' . $d->karyawan_id;
                $pot = $potonganMap[$kid] ?? null;

                $kar = $d->karyawan;
                if (!$kar && $d->karyawan_id) {
                    $kar = Karyawan::find($d->karyawan_id);
                }

                if (!isset($karyawanRekap[$kid])) {
                    $karyawanRekap[$kid] = [
                        'karyawan'         => $kar,
                        'total_uang_makan' => 0,
                        'total_lembur'     => 0,
                        'pot_utang'        => $pot ? (float) ($pot->pot_utang ?? 0) : 0,
                        'pot_bpjs'         => $pot ? (float) ($pot->pot_bpjs ?? 0) : 0,
                        'pot_pph'          => $pot ? (float) ($pot->pot_pph ?? 0) : 0,
                        'pot_terlambat'    => $pot ? (float) ($pot->pot_terlambat ?? 0) : 0,
                    ];
                }
                $karyawanRekap[$kid]['total_lembur'] += (float) ($d->total_akhir ?? 0);
            }
        }

        $items = collect();
        foreach ($karyawanRekap as $data) {
            $terima = max(0, (float) (
                $data['total_uang_makan']
                + $data['total_lembur']
                - $data['pot_terlambat']
                - $data['pot_utang']
                - $data['pot_bpjs']
                - $data['pot_pph']
            ));

            $items->push((object)[
                'karyawan' => $data['karyawan'],
                'amount'   => $terima,
            ]);
        }

        // Urutkan berdasarkan nama penerima (atas_nama atau nama_lengkap)
        $items = $items->sortBy(function ($item) {
            $karyawan = $item->karyawan;
            return $karyawan ? ($karyawan->atas_nama ?: $karyawan->nama_lengkap) : '';
        })->values();

        $this->dataCount = $items->count();
        $totalAmount = $items->sum('amount');

        $items->push((object)[
            'is_total_row' => true,
            'total_amount' => $totalAmount,
        ]);

        return $items;
    }

    public function headings(): array
    {
        return [
            'No',
            'Transaction ID',
            'Transfer Type',
            'Debited Acc.',
            'Beneficiary ID',
            'Credited Acc.',
            'Amount',
            'Eff. Date',
            'Transaction Purpose',
            'Currency',
            'Charges Type',
            'Charges Acc.',
            'Remark 1',
            'Receiver Name',
            'Receiver Cust. Type',
            'Receiver Cust. Residen',
            'Transaction Cd',
            'Beneficiary Email'
        ];
    }

    public function map($detail): array
    {
        if (is_object($detail) && isset($detail->is_total_row)) {
            return [
                '', // No
                '', // Transaction ID
                '', // Transfer Type
                '', // Debited Acc.
                '', // Beneficiary ID
                'TOTAL', // Credited Acc.
                $detail->total_amount, // Amount
                '', // Eff. Date
                '', // Transaction Purpose
                '', // Currency
                '', // Charges Type
                '', // Charges Acc.
                '', // Remark 1
                '', // Receiver Name
                '', // Receiver Cust. Type
                '', // Receiver Cust. Residen
                '', // Transaction Cd
                '', // Beneficiary Email
            ];
        }

        $karyawan = $detail->karyawan;
        
        $dateStr = $this->puml->tanggal_pranota ? $this->puml->tanggal_pranota->format('dmy') : date('dmy');
        $transactionId = '01' . $dateStr . '-' . str_pad($this->rowNumber, 3, '0', STR_PAD_LEFT);
        
        $creditedAcc = $karyawan ? trim((string)($karyawan->akun_bank ?: ($karyawan->no_rekening ?? ''))) : '';

        return [
            $this->rowNumber++,
            $transactionId, // Transaction ID
            'BCA', // Transfer Type
            '1682889955', // Debited Acc.
            '', // Beneficiary ID
            $creditedAcc, // Credited Acc.
            $detail->amount, // Amount
            $this->puml->tanggal_pranota ? $this->puml->tanggal_pranota->format('d/m/Y') : '', // Eff. Date
            '', // Transaction Purpose
            '', // Currency
            '', // Charges Type
            '1682889955', // Charges Acc.
            'UANG MAKAN', // Remark 1
            $karyawan ? ($karyawan->atas_nama ?: $karyawan->nama_lengkap) : '', // Receiver Name
            '', // Receiver Cust. Type
            '', // Receiver Cust. Residen
            '', // Transaction Cd
            '=HYPERLINK("mailto:alexindo.yakinprima@gmail.com", "alexindo.yakinprima@gmail.com")', // Beneficiary Email
        ];
    }

    public function columnFormats(): array
    {
        return [
            'G' => '#,##0', // Format kolom G (Amount) dengan pemisah ribuan
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->dataCount + 2; // +1 untuk header, +1 untuk baris TOTAL

        // Styling untuk Header (Baris 1)
        $sheet->getStyle('A1:R1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'], // Teks putih
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF1F4E78'], // Background biru gelap profesional
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Memberikan border ke semua cell data
        $sheet->getStyle('A1:R' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF808080'], // Border abu-abu
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Styling khusus untuk baris TOTAL (Baris terakhir)
        $sheet->getStyle('A' . $lastRow . ':R' . $lastRow)->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFF2F2F2'], // Background abu-abu muda
            ],
        ]);
        
        // Styling untuk kolom Beneficiary Email (Kolom R) agar teks berwarna biru & bergaris bawah
        $dataEnd = $lastRow - 1;
        if ($dataEnd >= 2) {
            $sheet->getStyle('R2:R' . $dataEnd)->applyFromArray([
                'font' => [
                    'color' => ['argb' => 'FF0000FF'],
                    'underline' => true,
                ],
            ]);
        }

        // Membekukan (Freeze) baris pertama agar header tetap terlihat saat di-scroll
        $sheet->freezePane('A2');

        return [];
    }
}
