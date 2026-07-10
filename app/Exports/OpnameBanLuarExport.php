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
use Carbon\Carbon;

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
        $startDate = Carbon::createFromDate($this->tahun, $this->bulan, 1)->startOfMonth()->toDateString();
        $endDate = Carbon::createFromDate($this->tahun, $this->bulan, 1)->endOfMonth()->toDateString();

        // Opname hanya untuk ban yang STATUS-nya 'Stok', KONDISI-nya 'asli'/'kanisir',
        // dan MASUK pada atau sebelum akhir bulan yang dipilih.
        $stockBans = StockBan::with(['namaStockBan'])
            ->where('status', 'Stok')
            ->whereIn('kondisi', ['asli', 'kanisir'])
            ->whereNotNull('tanggal_masuk')
            ->where('tanggal_masuk', '<=', $endDate)
            ->orderBy('lokasi')
            ->orderBy('kondisi')
            ->get();
            
        // Rekap Stok dari koleksi snapshot di atas
        $stokByLokasi = [];
        foreach ($stockBans as $ban) {
            $lokasi = $ban->lokasi ?: 'Tidak Diketahui';
            if (!isset($stokByLokasi[$lokasi])) {
                $stokByLokasi[$lokasi] = 0;
            }
            $stokByLokasi[$lokasi]++;
        }
            
        // Yang terpakai dan dikirim SELAMA BULAN TERSEBUT
        $terpakai = StockBan::whereBetween('tanggal_digunakan', [$startDate, $endDate])->count();
        $keBatam = StockBan::whereBetween('tanggal_kirim', [$startDate, $endDate])
                           ->where('status', 'Dikirim Ke Batam')->count();
        $kePinang = StockBan::whereBetween('tanggal_kirim', [$startDate, $endDate])
                            ->where('status', 'Dikirim Ke Tanjung Pinang')->count();

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
