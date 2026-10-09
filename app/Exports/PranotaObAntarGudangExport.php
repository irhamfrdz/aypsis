<?php

namespace App\Exports;

use App\Models\PranotaObAntarGudang;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PranotaObAntarGudangExport implements FromArray, ShouldAutoSize, WithColumnFormatting, WithStyles
{
    public function __construct(private readonly PranotaObAntarGudang $pranota) {}

    public function array(): array
    {
        $rows = [
            ['PRANOTA OB ANTAR GUDANG'],
            ['Nomor Pranota', $this->pranota->nomor_pranota],
            ['Tanggal Pranota', $this->pranota->tanggal_pranota],
            ['Dibuat Oleh', $this->pranota->creator?->name ?? '-'],
            ['Status Pembayaran', $this->pranota->status_pembayaran ?? 'Belum Lunas'],
            ['Keterangan', $this->pranota->keterangan ?? ''],
            [],
            ['No', 'Tanggal Tagihan', 'No. Kontainer', 'Nama Supir', 'Status Kontainer', 'Keterangan Rute', 'Biaya'],
        ];

        foreach ($this->pranota->items as $index => $item) {
            $tagihan = $item->tagihanOb;
            $rows[] = [
                $index + 1,
                $tagihan?->created_at?->format('d/m/Y H:i') ?? '-',
                $tagihan?->nomor_kontainer ?? '-',
                $tagihan?->nama_supir ?? '-',
                $tagihan?->status_kontainer ?? '-',
                $tagihan?->keterangan ?? '-',
                (float) ($tagihan?->biaya ?? 0),
            ];
        }

        $rows[] = ['', '', '', '', '', 'Subtotal', (float) $this->pranota->nominal];
        $rows[] = ['', '', '', '', '', 'Adjustment', (float) $this->pranota->adjustment];
        $rows[] = ['', '', '', '', '', 'Grand Total', (float) $this->pranota->grand_total];

        if ($this->pranota->alasan_adjustment) {
            $rows[] = ['Alasan Adjustment', $this->pranota->alasan_adjustment];
        }

        return $rows;
    }

    public function columnFormats(): array
    {
        return ['C' => NumberFormat::FORMAT_TEXT, 'G' => '#,##0'];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1:G1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A8:G8')->getFont()->setBold(true);
        $lastTotalRow = 11 + $this->pranota->items->count();
        $sheet->getStyle("F{$lastTotalRow}:G{$lastTotalRow}")->getFont()->setBold(true);

        return [];
    }
}
