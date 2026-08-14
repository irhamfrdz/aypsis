<?php

namespace App\Exports;

use App\Models\BiayaKapal;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ValuasiBiayaKapalExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $kapal;
    protected $jenisBiaya;
    protected $tanggalMulai;
    protected $tanggalAkhir;

    public function __construct($kapal, $jenisBiaya, $tanggalMulai, $tanggalAkhir)
    {
        $this->kapal = $kapal;
        $this->jenisBiaya = $jenisBiaya;
        $this->tanggalMulai = $tanggalMulai;
        $this->tanggalAkhir = $tanggalAkhir;
    }

    public function collection()
    {
        $query = BiayaKapal::with(['klasifikasiBiaya', 'vendor'])
            ->whereBetween('tanggal', [$this->tanggalMulai, $this->tanggalAkhir]);

        if ($this->kapal) {
            $kapal = $this->kapal;
            $query->where(function ($q) use ($kapal) {
                $q->whereJsonContains('nama_kapal', $kapal)
                  ->orWhere('nama_kapal', 'like', "%\"{$kapal}\"%")
                  ->orWhere('nama_kapal', 'like', "%{$kapal}%");
            });
        }

        if ($this->jenisBiaya) {
            $query->where('jenis_biaya', $this->jenisBiaya);
        }

        return $query->orderBy('tanggal', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Nomor Invoice',
            'Nomor Referensi',
            'Nama Kapal',
            'No Voyage',
            'No BL',
            'Jenis Biaya',
            'Vendor',
            'Keterangan',
            'Nominal',
            'PPN',
            'PPh',
            'Total Biaya',
            'Status Pembayaran'
        ];
    }

    public function map($row): array
    {
        static $no = 1;
        return [
            $no++,
            $row->tanggal ? $row->tanggal->format('d/M/Y') : '-',
            $row->nomor_invoice ?? '-',
            $row->nomor_referensi ?? '-',
            $row->display_nama_kapal,
            $row->display_no_voyage,
            $row->display_no_bl,
            $row->klasifikasiBiaya ? $row->klasifikasiBiaya->nama : ($row->jenis_biaya ?? '-'),
            $row->vendor ? $row->vendor->nama : ($row->nama_vendor ?? '-'),
            $row->keterangan ?? '-',
            $row->nominal ?? 0,
            $row->ppn ?? 0,
            $row->pph ?? 0,
            $row->total_biaya ?? 0,
            $row->status_pembayaran === 'paid' ? 'Lunas' : ($row->status_pembayaran === 'cancelled' ? 'Dibatalkan' : 'Pending')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
