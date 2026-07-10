<?php

namespace App\Exports;

use App\Models\StockBan;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use DB;

class OpnameBanLuarExport implements FromView, ShouldAutoSize, WithStyles, WithTitle
{
    protected $bulan;
    protected $tahun;

    public function __construct($bulan, $tahun)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function view(): View
    {
        $stockBans = StockBan::with(['namaStockBan'])
            ->whereIn('status', ['Stok', 'Rusak'])
            ->orderBy('lokasi')
            ->orderBy('kondisi')
            ->get();
            
        $stokByLokasi = StockBan::whereIn('status', ['Stok', 'Rusak'])
            ->select('lokasi', DB::raw('count(*) as total'))
            ->groupBy('lokasi')
            ->pluck('total', 'lokasi')
            ->toArray();
            
        $terpakai = StockBan::where('status', 'Terpakai')->count();
        $keBatam = StockBan::where('status', 'Dikirim Ke Batam')->count();
        $kePinang = StockBan::where('status', 'Dikirim Ke Tanjung Pinang')->count();

        return view('exports.opname-ban-luar', [
            'stockBans' => $stockBans,
            'bulan' => $this->bulan,
            'tahun' => $this->tahun,
            'stokByLokasi' => $stokByLokasi,
            'terpakai' => $terpakai,
            'keBatam' => $keBatam,
            'kePinang' => $kePinang,
        ]);
    }

    public function title(): string
    {
        return 'Opname ' . $this->bulan . '-' . $this->tahun;
    }

    public function styles(Worksheet $sheet)
    {
        // Styling is mostly handled by HTML table in blade view, but we can add some global formatting
        
        // Merge cells for titles
        $sheet->mergeCells('A1:I1');
        $sheet->mergeCells('A2:I2');

        // Style the title
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        $sheet->getStyle('A2')->getFont()->setBold(true);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        return [];
    }
}
