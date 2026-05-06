<?php

namespace App\Imports;

use App\Models\Buruh;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class BuruhImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Buruh([
            'nama'   => $row['nama'],
            'nik'    => $row['nik'] ?? null,
            'status' => isset($row['status']) && strtolower($row['status']) === 'aktif' ? 'aktif' : 'non-aktif',
        ]);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'nik' => 'nullable|string|max:50',
            'status' => 'nullable|string',
        ];
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'nama.required' => 'Kolom Nama wajib diisi.',
        ];
    }
}
