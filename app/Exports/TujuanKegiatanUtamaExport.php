<?php
namespace App\Exports;

use App\Models\TujuanKegiatanUtama;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TujuanKegiatanUtamaExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return TujuanKegiatanUtama::all();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Kode',
            'Cabang',
            'Wilayah',
            'Dari',
            'Ke',
            'Uang Jalan 20ft',
            'Uang Jalan 40ft',
            'Keterangan',
            'Liter',
            'Jarak dari Penjaringan (km)',
            'MEL 20ft',
            'MEL 40ft',
            'Ongkos Truk 20ft',
            'Ongkos Truk 40ft',
            'Antar Lokasi 20ft',
            'Antar Lokasi 40ft',
            'Status',
            'Dibuat',
            'Diupdate',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->kode,
            $row->cabang,
            $row->wilayah,
            $row->dari,
            $row->ke,
            $row->uang_jalan_20ft,
            $row->uang_jalan_40ft,
            $row->keterangan,
            $row->liter,
            $row->jarak_dari_penjaringan_km,
            $row->mel_20ft,
            $row->mel_40ft,
            $row->ongkos_truk_20ft,
            $row->ongkos_truk_40ft,
            $row->antar_lokasi_20ft,
            $row->antar_lokasi_40ft,
            $row->aktif ? 'Aktif' : 'Tidak Aktif',
            $row->created_at?->format('Y-m-d H:i:s'),
            $row->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
