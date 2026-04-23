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
                $sheet->getColumnDimension('B')->setWidth(18);
                $sheet->getColumnDimension('C')->setWidth(6);
                $sheet->getColumnDimension('D')->setWidth(8);
                $sheet->getColumnDimension('E')->setWidth(40);
                $sheet->getColumnDimension('F')->setWidth(10);
                $sheet->getColumnDimension('G')->setWidth(5);
                $sheet->getColumnDimension('H')->setWidth(18);

                // Styling for the whole sheet
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A1:Z{$highestRow}")->getFont()->setName('Arial');
                $sheet->getStyle("A1:Z{$highestRow}")->getFont()->setSize(10);

                // Center align column A and G
                $sheet->getStyle("A1:A{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle("G1:G{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                
                // Vertical align everything to top
                $sheet->getStyle("A1:Z{$highestRow}")->getAlignment()->setVertical(Alignment::VERTICAL_TOP);

                // Post-process columns C and E: replace ", " with newline for in-cell line breaks
                foreach (['C', 'E'] as $col) {
                    for ($row = 1; $row <= $highestRow; $row++) {
                        $cell = $sheet->getCell("{$col}{$row}");
                        $value = $cell->getValue();
                        
                        if ($value && is_string($value) && strpos($value, ', ') !== false) {
                            $cell->setValue(str_replace(', ', "," . chr(10), $value));
                            $sheet->getStyle("{$col}{$row}")->getAlignment()->setWrapText(true);
                        }
                    }
                }
            },
        ];
    }
}
