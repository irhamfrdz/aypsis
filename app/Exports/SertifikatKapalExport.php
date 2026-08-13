<?php

namespace App\Exports;

use App\Models\SertifikatKapal;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SertifikatKapalExport implements FromCollection, ShouldAutoSize, WithEvents, WithHeadings
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = SertifikatKapal::query();

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nama_sertifikat', 'like', '%'.$search.'%')
                    ->orWhere('name_certificate', 'like', '%'.$search.'%')
                    ->orWhere('nickname', 'like', '%'.$search.'%');
            });
        }

        if (!empty($this->filters['status']) && $this->filters['status'] !== 'all') {
            $query->where('status', $this->filters['status']);
        }

        $sertifikats = $query->orderBy('created_at', 'desc')->get();

        $rows = $sertifikats->map(function ($s, $index) {
            return [
                $index + 1,
                $s->nama_sertifikat,
                $s->name_certificate ?? '-',
                $s->nickname ?? '-',
                $s->status === 'aktif' ? 'Aktif' : 'Nonaktif',
            ];
        });

        return $rows;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Sertifikat (ID)',
            'Name of Certificate (EN)',
            'Nickname',
            'Status',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('A1:E1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A1:E1')->getFont()->setBold(true);
                $sheet->getStyle('A1:E1')->getFill()
                      ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                      ->getStartColor()->setARGB('FFE2E8F0'); // Light gray
            },
        ];
    }
}
