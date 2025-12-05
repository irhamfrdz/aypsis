<?php

namespace App\Exports;

// Use collect() helper instead of importing Collection to avoid name conflicts
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class MobilExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $rows;

    public function __construct($mobils)
    {
        // $mobils may be collection
        $this->rows = collect($mobils)->map(function ($mobil, $index) {
            return [
                $index + 1,
                $mobil['kode_no'] ?? ($mobil->kode_no ?? ''),
                $mobil['nomor_polisi'] ?? ($mobil->nomor_polisi ?? ''),
                data_get($mobil, 'karyawan.nik', ''),
                data_get($mobil, 'karyawan.nama_lengkap', ''),
                $mobil['lokasi'] ?? ($mobil->lokasi ?? ''),
                $mobil['merek'] ?? ($mobil->merek ?? ''),
                $mobil['jenis'] ?? ($mobil->jenis ?? ''),
                $mobil['tahun_pembuatan'] ?? ($mobil->tahun_pembuatan ?? ''),
                $mobil['bpkb'] ?? ($mobil->bpkb ?? ''),
                $mobil['no_mesin'] ?? ($mobil->no_mesin ?? ''),
                $mobil['nomor_rangka'] ?? ($mobil->nomor_rangka ?? ''),
                ($mobil['pajak_stnk'] ?? ($mobil->pajak_stnk ?? '')) ? (\Carbon\Carbon::parse($mobil['pajak_stnk'] ?? $mobil->pajak_stnk)->format('d/M/Y')) : '',
                ($mobil['pajak_plat'] ?? ($mobil->pajak_plat ?? '')) ? (\Carbon\Carbon::parse($mobil['pajak_plat'] ?? $mobil->pajak_plat)->format('d/M/Y')) : '',
                $mobil['no_kir'] ?? ($mobil->no_kir ?? ''),
                ($mobil['pajak_kir'] ?? ($mobil->pajak_kir ?? '')) ? (\Carbon\Carbon::parse($mobil['pajak_kir'] ?? $mobil->pajak_kir)->format('d/M/Y')) : '',
                $mobil['atas_nama'] ?? ($mobil->atas_nama ?? ''),
                $mobil['pemakai'] ?? ($mobil->pemakai ?? ''),
                $mobil['asuransi'] ?? ($mobil->asuransi ?? ''),
                ($mobil['jte_asuransi'] ?? ($mobil->jte_asuransi ?? '')) ? (\Carbon\Carbon::parse($mobil['jte_asuransi'] ?? $mobil->jte_asuransi)->format('d/M/Y')) : '',
                $mobil['warna_plat'] ?? ($mobil->warna_plat ?? ''),
                $mobil['catatan'] ?? ($mobil->catatan ?? ''),
                isset($mobil['created_at']) ? (\Carbon\Carbon::parse($mobil['created_at'])->format('d/M/Y H:i')) : (isset($mobil->created_at) ? $mobil->created_at->format('d/M/Y H:i') : ''),
                isset($mobil['updated_at']) ? (\Carbon\Carbon::parse($mobil['updated_at'])->format('d/M/Y H:i')) : (isset($mobil->updated_at) ? $mobil->updated_at->format('d/M/Y H:i') : ''),
            ];
        });
    }

    public function collection()
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Aktiva',
            'Nomor Polisi',
            'NIK Karyawan',
            'Nama Karyawan',
            'Lokasi',
            'Merek',
            'Jenis',
            'Tahun Pembuatan',
            'BPKB',
            'No. Mesin',
            'No. Rangka',
            'Pajak STNK',
            'Pajak Plat',
            'No. KIR',
            'Pajak KIR',
            'Atas Nama',
            'Pemakai',
            'Asuransi',
            'JTE Asuransi',
            'Warna Plat',
            'Catatan',
            'Dibuat Tanggal',
            'Diperbarui Tanggal'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A1:Z1")->getFont()->setBold(true);
                $sheet->getStyle("A1:Z{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            }
        ];
    }
}
