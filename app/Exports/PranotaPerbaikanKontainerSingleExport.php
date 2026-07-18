<?php

namespace App\Exports;

use App\Models\PranotaPerbaikanKontainer;
use App\Models\PerbaikanKontainer;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PranotaPerbaikanKontainerSingleExport implements FromCollection, ShouldAutoSize, WithColumnFormatting, WithEvents, WithHeadings
{
    protected $pranota;
    protected $printType;

    public function __construct($pranotaId, $printType = null)
    {
        $this->pranota = PranotaPerbaikanKontainer::findOrFail($pranotaId);
        $this->printType = $printType;
    }

    public function collection()
    {
        $rows = collect();
        if (is_array($this->pranota->items)) {
            $i = 1;
            foreach ($this->pranota->items as $item) {
                $biayaRiil = floatval($item['biaya_riil'] ?? 0);
                $estimasi = floatval($item['estimasi_biaya'] ?? 0);
                $biayaCat = floatval($item['biaya_cat'] ?? 0);
                $biayaPerbaikanOnly = ($biayaRiil > 0) ? $biayaRiil : $estimasi;

                if ($this->printType === 'cat') {
                    if ($biayaCat <= 0) continue;
                    $biayaTerpakai = $biayaCat;
                    $bengkelVendor = $item['vendor_cat'] ?? '-';
                    $keterangan = "Pengecatan " . (isset($item['jenis_cat']) && $item['jenis_cat'] === 'cat_full' ? 'Full' : 'Sebagian');
                } elseif ($this->printType === 'perbaikan') {
                    if ($biayaPerbaikanOnly <= 0) continue;
                    $biayaTerpakai = $biayaPerbaikanOnly;
                    $bengkelVendor = $item['bengkel'] ?? '-';
                    $perbaikan = PerbaikanKontainer::find($item['id'] ?? null);
                    $keterangan = $item['keterangan_kerusakan'] ?? ($perbaikan->keterangan_kerusakan ?? '-');
                } else {
                    $biayaTerpakai = $biayaPerbaikanOnly + $biayaCat;
                    $bengkelVendor = $item['bengkel'] ?? '-';
                    $perbaikan = PerbaikanKontainer::find($item['id'] ?? null);
                    $ketKerusakan = $item['keterangan_kerusakan'] ?? ($perbaikan->keterangan_kerusakan ?? '-');
                    $keterangan = $ketKerusakan;
                    if (!empty($item['is_cat']) && $biayaCat > 0) {
                        $jenisCat = isset($item['jenis_cat']) && $item['jenis_cat'] === 'cat_full' ? 'Full' : 'Sebagian';
                        $keterangan .= " ( + Cat $jenisCat)";
                    }
                }

                $perbaikan = PerbaikanKontainer::find($item['id'] ?? null);
                $tanggalPerbaikan = $perbaikan && $perbaikan->tanggal_masuk ? $perbaikan->tanggal_masuk->format('d/m/Y') : '-';

                $rows->push([
                    'NO' => $i++,
                    'NO. PERBAIKAN' => $item['no_perbaikan'] ?? '-',
                    'TGL. PERBAIKAN' => $tanggalPerbaikan,
                    'NO. KONTAINER' => $item['no_kontainer'] ?? '-',
                    'UKURAN & TIPE' => ($item['ukuran'] ?? '') . 'FT ' . ($item['tipe'] ?? ''),
                    'BENGKEL/VENDOR' => $bengkelVendor,
                    'KETERANGAN' => $keterangan,
                    'ESTIMASI PERBAIKAN' => $this->printType !== 'cat' ? $estimasi : null,
                    'REALISASI PERBAIKAN' => $this->printType !== 'cat' ? $biayaRiil : null,
                    'BIAYA PERBAIKAN' => $this->printType !== 'cat' ? $biayaPerbaikanOnly : null,
                    'BIAYA CAT' => $this->printType !== 'perbaikan' ? $biayaCat : null,
                    'TOTAL BIAYA' => $biayaTerpakai,
                ]);
            }
        }
        return $rows;
    }

    public function headings(): array
    {
        return [
            'NO',
            'NO. PERBAIKAN',
            'TGL. PERBAIKAN',
            'NO. KONTAINER',
            'UKURAN & TIPE',
            'BENGKEL/VENDOR',
            'KETERANGAN',
            'ESTIMASI PERBAIKAN',
            'REALISASI PERBAIKAN',
            'BIAYA PERBAIKAN',
            'BIAYA CAT',
            'TOTAL BIAYA',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'H' => '#,##0',
            'I' => '#,##0',
            'J' => '#,##0',
            'K' => '#,##0',
            'L' => '#,##0',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('A1:L1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A1:L1')->getFont()->setBold(true);
            },
        ];
    }
}
