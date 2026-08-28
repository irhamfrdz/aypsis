<?php

namespace App\Exports;

use App\Models\MasterPengirimPenerima;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MasterPengirimPenerimaDataExport implements FromQuery, WithHeadings, WithMapping
{
    public function query()
    {
        return MasterPengirimPenerima::query()->where('status', 'active');
    }

    public function headings(): array
    {
        return [
            'KODE',
            'NAMA',
            'PIC',
            'TELEPON',
            'CONTACT PERSON',
        ];
    }

    public function map($row): array
    {
        return [
            $row->kode,
            $row->nama,
            $row->pic,
            $row->telepon,
            $row->contact_person,
        ];
    }
}
