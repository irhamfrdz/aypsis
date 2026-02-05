<?php

namespace App\Imports;

use App\Models\Penerima;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PenerimaImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Penerima([
            'nama_penerima' => $row['nama_penerima'],
            'alamat' => $row['alamat'] ?? null,
            'npwp' => $row['npwp'] ?? null,
            'nitku' => $row['nitku'] ?? null,
            'catatan' => $row['catatan'] ?? null,
            'status' => isset($row['status']) && in_array(strtolower($row['status']), ['active', 'inactive']) ? strtolower($row['status']) : 'active',
            'iu_bp_kawasan' => isset($row['iu_bp_kawasan']) && in_array(strtolower($row['iu_bp_kawasan']), ['ada', 'tidak ada']) ? strtolower($row['iu_bp_kawasan']) : 'tidak ada',
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_penerima' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'npwp' => 'nullable|string',
            'nitku' => 'nullable|string',
            'catatan' => 'nullable|string',
            'status' => 'nullable|string|in:active,inactive',
            'iu_bp_kawasan' => 'nullable|string|in:ada,tidak ada',
        ];
    }
}
