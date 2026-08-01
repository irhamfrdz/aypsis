<?php

namespace App\Exports;

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

class PranotaUangMakanAutoTransferExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting, ShouldAutoSize, WithStyles
{
    protected $pranota;
    protected $rowNumber = 1;

    public function __construct($pranota)
    {
        $this->pranota = $pranota;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $details = $this->pranota->details->sortBy(function ($detail) {
            $karyawan = $detail->karyawan;
            return $karyawan ? ($karyawan->atas_nama ?: $karyawan->nama_lengkap) : '';
        })->values();

        $totalAmount = $details->sum('total_akhir');

        $details->push((object)[
            'is_total_row' => true,
            'total_amount' => $totalAmount
        ]);

        return $details;
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
        
        $dateStr = $this->pranota->tanggal_pranota ? $this->pranota->tanggal_pranota->format('dmy') : date('dmy');
        $transactionId = '01' . $dateStr . '-' . str_pad($this->rowNumber, 3, '0', STR_PAD_LEFT);
        
        return [
            $this->rowNumber++,
            $transactionId, // Transaction ID
            'BCA', // Transfer Type - standard value, can be empty if not needed
            '1682889955', // Debited Acc.
            '', // Beneficiary ID
            $karyawan ? $karyawan->akun_bank : '', // Credited Acc.
            $detail->total_akhir, // Amount
            $this->pranota->tanggal_pranota ? $this->pranota->tanggal_pranota->format('d/m/Y') : '', // Eff. Date
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
        $lastRow = $this->pranota->details->count() + 2; // +1 untuk header, +1 untuk baris TOTAL

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
                    'color' => ['argb' => 'FF808080'], // Border abu-abu agar tidak terlalu pekat
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
        
        // Membekukan (Freeze) baris pertama agar header tetap terlihat saat di-scroll
        $sheet->freezePane('A2');

        return [];
    }
}
