<?php

namespace App\Imports;

use App\Models\PricelistBuruh;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Auth;

class PricelistBuruhImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Normalize tipe value - ignore "-" or empty values
        $tipe = null;
        if (!empty($row['tipe']) && $row['tipe'] !== '-') {
            if (in_array($row['tipe'], ['Full', 'Empty'])) {
                $tipe = $row['tipe'];
            }
        }

        // Convert size to string (Excel may read it as numeric)
        $size = null;
        if (!empty($row['size']) && $row['size'] !== '-') {
            $size = (string) $row['size'];
        }

        return new PricelistBuruh([
            'barang' => $row['barang'],
            'size' => $size,
            'tipe' => $tipe,
            'tarif' => $row['tarif'],
            'is_active' => isset($row['status']) && strtolower($row['status']) === 'aktif',
            'keterangan' => !empty($row['keterangan']) && $row['keterangan'] !== '-' ? $row['keterangan'] : null,
            'created_by' => Auth::id(),
        ]);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'barang' => 'required|string|max:255',
            'size' => 'nullable|string|max:255',
            'tarif' => 'required|numeric|min:0',
            'status' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ];
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'barang.required' => 'Kolom Barang wajib diisi.',
            'tarif.required' => 'Kolom Tarif wajib diisi.',
            'tarif.numeric' => 'Kolom Tarif harus berupa angka.',
            'tarif.min' => 'Kolom Tarif tidak boleh kurang dari 0.',
            'tipe.in' => 'Kolom Tipe harus Full atau Empty.',
        ];
    }
}
