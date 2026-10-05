<?php

namespace App\Exports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AssetExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    protected $request;

    public function __construct($request = null)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Asset::with(['creator', 'karyawan']);

        if ($this->request) {
            if ($this->request->filled('search')) {
                $search = $this->request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('kode_asset', 'like', "%{$search}%")
                        ->orWhere('nama_asset', 'like', "%{$search}%")
                        ->orWhereHas('karyawan', function ($kq) use ($search) {
                            $kq->where('nama_lengkap', 'like', "%{$search}%")
                                ->orWhere('nama_panggilan', 'like', "%{$search}%")
                                ->orWhere('nik', 'like', "%{$search}%");
                        });
                });
            }

            if ($this->request->filled('karyawan_id')) {
                $query->where('karyawan_id', $this->request->karyawan_id);
            }

            if ($this->request->filled('kategori')) {
                $query->where('kategori', $this->request->kategori);
            }

            if ($this->request->filled('status')) {
                $query->where('status', $this->request->status);
            }

            if ($this->request->filled('kondisi')) {
                $query->where('kondisi', $this->request->kondisi);
            }
        }

        return $query->orderBy('kode_asset', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Asset',
            'Nama Asset',
            'Kategori',
            'Tanggal Perolehan',
            'Kondisi',
            'Status',
            'Pemegang Asset',
            'Tanggal Tanda Terima',
            'Vendor / Supplier',
            'No. Faktur',
            'Keterangan',
        ];
    }

    /**
     * @param  Asset  $asset
     */
    public function map($asset): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $asset->kode_asset,
            $asset->nama_asset,
            $asset->kategori,
            $asset->tanggal_perolehan ? $asset->tanggal_perolehan->format('Y-m-d') : '-',
            $asset->kondisi,
            $asset->status,
            $asset->karyawan ? ($asset->karyawan->nama_lengkap.($asset->karyawan->nik ? ' ('.$asset->karyawan->nik.')' : '')) : '-',
            $asset->tanggal_tanda_terima ? $asset->tanggal_tanda_terima->format('Y-m-d') : '-',
            $asset->vendor ?? '-',
            $asset->nomor_faktur ?? '-',
            $asset->keterangan ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1E40AF'],
                ],
            ],
        ];
    }
}
