<?php

namespace App\Exports;

use App\Models\Karyawan;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class KaryawanBpjsDataExport extends DefaultValueBinder implements FromQuery, WithCustomValueBinder, WithHeadings, WithMapping
{
    public function query()
    {
        return Karyawan::query()->select(['id', 'nik', 'nama_lengkap', 'group_jkn', 'tipe_jkn', 'dpp_jkn', 'group_bp_jamsostek', 'tipe_bp_jamsostek', 'dpp_bp_jamsostek', 'cabang_bpjs'])->orderBy('nama_lengkap')->orderBy('id');
    }

    public function headings(): array
    {
        return ['nik', 'nama', 'Group JKN', 'Tipe JKN', 'DPP JKN', 'Group BP Jamsostek', 'Tipe BP Jamsostek', 'DPP BP Jamsostek', 'Cabang BPJS'];
    }

    public function map($karyawan): array
    {
        return [$karyawan->nik, $karyawan->nama_lengkap, $karyawan->group_jkn, $karyawan->tipe_jkn, $karyawan->dpp_jkn, $karyawan->group_bp_jamsostek, $karyawan->tipe_bp_jamsostek, $karyawan->dpp_bp_jamsostek, $karyawan->cabang_bpjs];
    }

    public function bindValue(Cell $cell, $value): bool
    {
        if ($cell->getColumn() === 'A' && $value !== null) {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);

            return true;
        }

        return parent::bindValue($cell, $value);
    }
}
