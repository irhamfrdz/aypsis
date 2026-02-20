<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ManifestTableExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $manifests;

    public function __construct($manifests)
    {
        $this->manifests = $manifests;
    }

    public function collection()
    {
        $rows = $this->manifests->map(function($m, $index) {
            $tandaTerima = $m->prospek ? $m->prospek->tandaTerima : null;

            // Logic untuk Kuantitas & Satuan (PKGS)
            $qtyUnit = '';
            // Cek manifest dulu
            if (!empty($m->kuantitas) || !empty($m->satuan)) {
                $qtyUnit = trim(($m->kuantitas ?? '') . ' ' . ($m->satuan ?? ''));
            } 
            // Fallback ke Tanda Terima
            elseif ($tandaTerima) {
                if (!empty($tandaTerima->dimensi_details) && is_array($tandaTerima->dimensi_details)) {
                    $parts = [];
                    foreach ($tandaTerima->dimensi_details as $item) {
                        $q = $item['jumlah'] ?? '';
                        $s = $item['satuan'] ?? '';
                        if ($q || $s) $parts[] = trim("$q $s");
                    }
                    $qtyUnit = implode("\n", $parts);
                } else {
                    // Fallback ke kolom biasa jika dimensi_details kosong tapi ada jumlah/satuan
                    $q = $tandaTerima->jumlah;
                    $s = $tandaTerima->satuan;
                    if ($q || $s) {
                        $qtyUnit = trim(($q ?? '') . ' ' . ($s ?? ''));
                    }
                }
            }

            // Logic untuk Nama Barang (DESCRIPTION OF GOODS)
            $goodsName = '';
            // Cek manifest dulu
            if (!empty($m->nama_barang)) {
                $goodsName = $m->nama_barang;
            } 
            // Fallback ke Tanda Terima
            elseif ($tandaTerima) {
                if (!empty($tandaTerima->dimensi_details) && is_array($tandaTerima->dimensi_details)) {
                    $names = [];
                    foreach ($tandaTerima->dimensi_details as $item) {
                        $names[] = $item['nama_barang'] ?? '';
                    }
                    $goodsName = implode("\n", $names);
                } elseif (!empty($tandaTerima->nama_barang)) {
                    if (is_array($tandaTerima->nama_barang)) {
                        $goodsName = implode(", ", $tandaTerima->nama_barang);
                    } else {
                        $goodsName = $tandaTerima->nama_barang;
                    }
                }
            }

            return [
                $index + 1,
                $m->nomor_urut ?? '-',
                $m->nomor_bl,
                $m->nomor_tanda_terima ?? '-',
                $m->nomor_kontainer,
                $m->no_seal ?? '-',
                $m->tipe_kontainer,
                $m->size_kontainer,
                $qtyUnit,
                $goodsName,
                $m->pengirim,
                $m->penerima,
                $m->prospek && $m->prospek->tandaTerima ? $m->prospek->tandaTerima->meter_kubik : ($m->volume ?? '-'),
                $m->prospek && $m->prospek->tandaTerima ? $m->prospek->tandaTerima->tonase : ($m->tonnage ?? '-'),
            ];
        });

        return $rows;
    }

    public function headings(): array
    {
        return [
            'No',
            'No. Urut',
            'No. BL',
            'No. Tanda Terima',
            'MARK AND NUMBERS',
            'SEAL NO.',
            'Tipe',
            'Size',
            'PKGS',
            'DESCRIPTION OF GOODS',
            'Pengirim',
            'Penerima',
            'Volume',
            'Tonase',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Merge header for DESCRIPTION OF GOODS (Column I and J)
                $sheet->mergeCells('I1:J1');
                $sheet->setCellValue('I1', 'DESCRIPTION OF GOODS');

                $sheet->getStyle('A1:N1')->getFont()->setBold(true);
                $sheet->getStyle('A1:N1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A1:N1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                
                // Add cell borders and wrap text to all data
                $lastRow = count($this->manifests) + 1;
                $range = 'A1:N' . $lastRow;
                $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $sheet->getStyle($range)->getAlignment()->setWrapText(true);
                $sheet->getStyle($range)->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
            }
        ];
    }
}
