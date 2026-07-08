<?php

namespace App\Exports;

use App\Models\Penerima;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PenerimaExport implements FromCollection, ShouldAutoSize, WithEvents, WithHeadings
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Penerima::query();

        // Apply search filter if provided
        if (! empty($this->filters['search'])) {
            $searchTerm = $this->filters['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nama_penerima', 'LIKE', '%'.$searchTerm.'%')
                    ->orWhere('contact_person', 'LIKE', '%'.$searchTerm.'%')
                    ->orWhere('catatan', 'LIKE', '%'.$searchTerm.'%');
            });
        }

        // Apply quick filters if provided
        if (! empty($this->filters['filter'])) {
            $filter = $this->filters['filter'];
            if ($filter === 'no_alamat') {
                $query->where(function ($q) {
                    $q->whereNull('alamat')
                        ->orWhere('alamat', '')
                        ->orWhere('alamat', '-');
                });
            } elseif ($filter === 'similar') {
                $allPenerimas = Penerima::select('id', 'nama_penerima')->get();
                $grouped = [];
                foreach ($allPenerimas as $p) {
                    $clean = strtoupper($p->nama_penerima);
                    $clean = preg_replace('/\b(PT|CV|UD|TB|TOKO|Tbk)\b\.?/i', '', $clean);
                    $clean = preg_replace('/[^A-Z0-9]/', '', $clean);
                    $clean = trim($clean);
                    if (! empty($clean)) {
                        $grouped[$clean][] = $p->id;
                    }
                }
                $duplicateIds = [];
                foreach ($grouped as $ids) {
                    if (count($ids) > 1) {
                        $duplicateIds = array_merge($duplicateIds, $ids);
                    }
                }
                $query->whereIn('id', $duplicateIds);
            }
        }

        return $query->orderBy('created_at', 'desc')->get()->map(function ($penerima, $index) {
            return [
                $index + 1,
                $penerima->nama_penerima,
                $penerima->pic ?? '-',
                $penerima->telepon ?? '-',
                $penerima->alamat ?? '-',
                $penerima->npwp ?? '-',
                $penerima->nitku ?? '-',
                $penerima->catatan ?? '-',
                $penerima->iu_bp_kawasan === 'ada' ? 'Ada' : 'Tidak Ada',
                $penerima->status === 'active' ? 'Aktif' : 'Tidak Aktif',
                $penerima->created_at ? $penerima->created_at->format('d/m/Y H:i') : '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Penerima',
            'PIC',
            'Telepon',
            'Alamat',
            'NPWP',
            'NITKU',
            'Catatan',
            'IU BP Kawasan',
            'Status',
            'Tanggal Dibuat',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Style the header row
                $event->sheet->getStyle('A1:K1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '4F46E5'], // Indigo color
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Center align the No column
                $event->sheet->getStyle('A2:A'.($event->sheet->getHighestRow()))
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Center align the IU BP Kawasan column
                $event->sheet->getStyle('I2:I'.($event->sheet->getHighestRow()))
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Center align the Status column
                $event->sheet->getStyle('J2:J'.($event->sheet->getHighestRow()))
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Add border to all cells
                $event->sheet->getStyle('A1:K'.($event->sheet->getHighestRow()))
                    ->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['rgb' => 'CCCCCC'],
                            ],
                        ],
                    ]);

                // Set row height for header
                $event->sheet->getDelegate()->getRowDimension(1)->setRowHeight(25);
            },
        ];
    }
}
