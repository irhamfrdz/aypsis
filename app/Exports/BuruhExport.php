<?php

namespace App\Exports;

use App\Models\Buruh;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BuruhExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = Buruh::query();
        
        if ($this->search) {
            $query->where(function($q) {
                $q->where('nama', 'LIKE', "%{$this->search}%")
                  ->orWhere('nik', 'LIKE', "%{$this->search}%");
            });
        }

        return $query->orderBy('nama', 'asc')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No',
            'Nama',
            'NIK',
            'Status',
        ];
    }

    /**
     * @var Buruh $item
     */
    public function map($item): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $item->nama,
            $item->nik ?? '-',
            ucfirst($item->status),
        ];
    }

    /**
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
