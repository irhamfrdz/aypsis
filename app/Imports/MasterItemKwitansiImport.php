<?php

namespace App\Imports;

use App\Models\MasterItemKwitansi;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MasterItemKwitansiImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new MasterItemKwitansi([
            'kode' => $row['kode'],
            'nama_item' => $row['nama_item'],
            'group' => $row['group'],
            'keterangan' => $row['keterangan'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'kode' => 'required|string|max:50|unique:master_item_kwitansis,kode',
            'nama_item' => 'required|string|max:255',
            'group' => 'required|string|max:100',
            'keterangan' => 'nullable|string',
        ];
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'kode.required' => 'Kolom Kode wajib diisi.',
            'kode.unique' => 'Kode :input sudah digunakan.',
            'nama_item.required' => 'Kolom Nama Item wajib diisi.',
            'group.required' => 'Kolom Group wajib diisi.',
        ];
    }
}
