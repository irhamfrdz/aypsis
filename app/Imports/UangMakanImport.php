<?php

namespace App\Imports;

use App\Models\Karyawan;
use App\Models\UangMakan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class UangMakanImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $karyawan = Karyawan::where('nik', $row['nik'])->first();
        if (!$karyawan) {
            return null; // Skip if NIK not found
        }

        $tanggal = is_numeric($row['tanggal']) 
            ? Date::excelToDateTimeObject($row['tanggal'])->format('Y-m-d')
            : date('Y-m-d', strtotime($row['tanggal']));

        $exists = UangMakan::where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', $tanggal)
            ->exists();

        if ($exists) {
            return null; // Skip if data already exists
        }

        return new UangMakan([
            'karyawan_id' => $karyawan->id,
            'tanggal'     => $tanggal,
            'nominal'     => $row['nominal'] ?? 0,
            'keterangan'  => $row['keterangan'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nik'     => 'required',
            'tanggal' => 'required',
            'nominal' => 'required|numeric|min:0',
        ];
    }
}
