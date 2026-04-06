<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class InsuranceRequestExport implements FromView, ShouldAutoSize, WithEvents
{
    protected $receipts;
    protected $vendor;
    protected $shipName;
    protected $requestDate;

    public function __construct($receipts, $vendor = null, $shipName = null, $requestDate = null)
    {
        $this->receipts = $receipts;
        
        // Prioritize ship name from insurance records if possible
        $first = $receipts->first();
        if ($first && isset($first->insurance_ship) && $first->insurance_ship) {
            $this->shipName = $first->insurance_ship;
        } else {
            $this->shipName = $shipName ?: 'KM. ALKEN PESONA';
        }
        
        $this->vendor = $vendor;
        $this->requestDate = $requestDate ? \Carbon\Carbon::parse($requestDate)->translatedFormat('d F Y') : date('d F Y');
    }

    public function view(): View
    {
        // Group receipts by 'numbering' (user-input sequence) if available.
        // If no numbering is set, use a unique key to keep them as separate individual entries.
        $grouped = $this->receipts->groupBy(function ($item) {
            return $item->numbering ?: 'unassigned_' . $item->type . '_' . $item->id;
        });

        return view('exports.insurance_request', [
            'grouped' => $grouped,
            'vendor' => $this->vendor,
            'shipName' => $this->shipName,
            'requestDate' => $this->requestDate
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Set Column Widths (Manual override because ShouldAutoSize is not always perfect)
                $sheet->getColumnDimension('A')->setWidth(5);
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(50);
                $sheet->getColumnDimension('G')->setWidth(5);
                $sheet->getColumnDimension('H')->setWidth(20);

                // Styling for the whole sheet
                $sheet->getStyle('A1:Z500')->getFont()->setName('Arial');
                $sheet->getStyle('A1:Z500')->getFont()->setSize(10);

                // Center align column A and G
                $sheet->getStyle('A1:A500')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('G1:G500')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                
                // Vertical align everything to top
                $sheet->getStyle('A1:Z500')->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
            },
        ];
    }
}
