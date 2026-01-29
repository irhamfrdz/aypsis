<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class SuratJalanBongkaranTableExport implements FromView, ShouldAutoSize, WithEvents
{
    protected $data;
    protected $mode;
    protected $nama_kapal;
    protected $no_voyage;

    public function __construct($data, $mode = 'manifest', $nama_kapal = '', $no_voyage = '')
    {
        $this->data = $data;
        $this->mode = $mode;
        $this->nama_kapal = $nama_kapal;
        $this->no_voyage = $no_voyage;
    }

    public function view(): View
    {
        return view('exports.surat-jalan-bongkaran', [
            'data' => $this->data,
            'mode' => $this->mode,
            'nama_kapal' => $this->nama_kapal,
            'no_voyage' => $this->no_voyage,
            'date' => now()->format('d-M-y'), // Format e.g., 17-Jan-26 (using current date as per request context, or ideally voyage date if available)
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Additional styling can be applied here if needed beyond HTML/CSS
                // For example, ensuring specific column widths or print layout
                $sheet = $event->sheet->getDelegate();
                
                // Example: Set specific column widths if AutoSize isn't perfect
                // $sheet->getColumnDimension('A')->setWidth(5);
                // $sheet->getColumnDimension('B')->setWidth(8);
            }
        ];
    }
}
