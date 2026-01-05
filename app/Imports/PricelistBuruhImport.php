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
     * Prepare data for validation - convert size to string before validation
     */
    public function prepareForValidation($data, $index)
    {
        // Convert barang to uppercase
        if (isset($data['barang'])) {
            $data['barang'] = strtoupper($data['barang']);
        }
        
        // Convert size to string if it's numeric (Excel reads numbers as numeric type)
        if (isset($data['size']) && !is_string($data['size'])) {
            $data['size'] = (string) $data['size'];
        }
        
        // Convert "-" to null for size validation
        if (isset($data['size']) && $data['size'] === '-') {
            $data['size'] = null;
        }
        
        // Convert size to uppercase if not null
        if (isset($data['size']) && $data['size'] !== null) {
            $data['size'] = strtoupper($data['size']);
        }
        
        // Convert tipe to uppercase
        if (isset($data['tipe']) && $data['tipe'] !== '-') {
            $data['tipe'] = strtoupper($data['tipe']);
        }
        
        // Convert keterangan to uppercase
        if (isset($data['keterangan']) && $data['keterangan'] !== '-') {
            $data['keterangan'] = strtoupper($data['keterangan']);
        }
        
        return $data;
    }

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
            // Accept both uppercase and proper case after prepareForValidation converted to uppercase
            if (in_array(strtoupper($row['tipe']), ['FULL', 'EMPTY'])) {
                $tipe = ucfirst(strtolower($row['tipe'])); // Store as 'Full' or 'Empty' for consistency
            }
        }

        // Convert size to string (Excel may read it as numeric)
        $size = null;
        if (!empty($row['size']) && $row['size'] !== '-') {
            $size = strtoupper((string) $row['size']);
        }

        return new PricelistBuruh([
            'barang' => strtoupper($row['barang']),
            'size' => $size,
            'tipe' => $tipe,
            'tarif' => $row['tarif'],
            'is_active' => isset($row['status']) && strtolower($row['status']) === 'aktif',
            'keterangan' => !empty($row['keterangan']) && $row['keterangan'] !== '-' ? strtoupper($row['keterangan']) : null,
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
