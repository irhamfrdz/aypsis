<?php

namespace App\Imports;

use App\Models\MasterNamaBarangAmprahan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MasterNamaBarangAmprahanImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new MasterNamaBarangAmprahan([
            'nama_barang' => $row['nama_barang'],
            'status'      => strtolower($row['status'] ?? 'active') == 'active' ? 'active' : 'inactive',
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_barang' => 'required|string|max:255|unique:master_nama_barang_amprahans,nama_barang',
            'status'      => 'nullable|string|in:active,inactive,Active,Inactive',
        ];
    }
}
