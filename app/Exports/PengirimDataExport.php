<?php

namespace App\Exports;

use App\Models\Pengirim;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PengirimDataExport implements FromQuery, WithHeadings, WithMapping
{
    public function query()
    {
        return Pengirim::query()->where('status', 'active');
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
            $row->nama_pengirim, // In Pengirim model it is nama_pengirim
            $row->pic,
            $row->telepon,
            $row->contact_person,
        ];
    }
}
