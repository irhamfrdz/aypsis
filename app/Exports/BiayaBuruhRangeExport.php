<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class BiayaBuruhRangeExport implements FromView, ShouldAutoSize, WithEvents
{
    protected $biayaKapals;
    protected $tanggalMulai;
    protected $tanggalAkhir;

    public function __construct($biayaKapals, $tanggalMulai, $tanggalAkhir)
    {
        $this->biayaKapals = $biayaKapals;
        $this->tanggalMulai = $tanggalMulai;
        $this->tanggalAkhir = $tanggalAkhir;
    }

    public function view(): View
    {
        $processedData = [];

        foreach ($this->biayaKapals as $biayaKapal) {
            // Get grouped details
            $groupedDetails = $biayaKapal->barangDetails->groupBy(function ($item) {
                return ($item->kapal ?? '-').'|'.($item->voyage ?? '-').'|'.($item->nomor_referensi ?? '-');
            });

            // Combined barang
            $combinedBarang = $biayaKapal->barangDetails
                ->filter(function ($item) {
                    return $item->pricelist_buruh_id !== null;
                })
                ->groupBy('pricelist_buruh_id')
                ->map(function ($items) {
                    $first = $items->first();

                    return [
                        'barang' => $first->pricelistBuruh->barang ?? '-',
                        'harga_satuan' => $first->pricelistBuruh->tarif ?? 0,
                        'jumlah' => $items->sum('jumlah'),
                        'subtotal' => $items->sum('subtotal'),
                    ];
                })->values();

            $totalAdjustments = $biayaKapal->barangDetails
                ->groupBy(function ($item) {
                    return ($item->kapal ?? '-').'|'.($item->voyage ?? '-');
                })
                ->map(function ($group) {
                    return $group->first()->adjustment ?? 0;
                })
                ->sum();

            $overallTotal = $combinedBarang->sum('subtotal') + $totalAdjustments;

            // Grouped buruhs
            $tenagaKerjaGroups = $biayaKapal->tenagaKerjaDetails->groupBy(function ($item) {
                return ($item->kapal ?? '-').' - '.($item->voyage ?? '-');
            });
            
            $processedData[] = [
                'biayaKapal' => $biayaKapal,
                'groupedDetails' => $groupedDetails,
                'combinedBarang' => $combinedBarang,
                'totalAdjustments' => $totalAdjustments,
                'overallTotal' => $overallTotal,
                'tenagaKerjaGroups' => $tenagaKerjaGroups,
            ];
        }

        return view('exports.biaya_buruh_range', [
            'processedData' => $processedData,
            'tanggalMulai' => $this->tanggalMulai,
            'tanggalAkhir' => $this->tanggalAkhir,
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('A1:Z1000')->getFont()->setName('Arial');
                $sheet->getStyle('A1:Z1000')->getFont()->setSize(10);
            },
        ];
    }
}