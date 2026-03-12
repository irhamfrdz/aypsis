<?php

namespace App\Imports;

use App\Models\MasterPricelistFreight;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Facades\Log;

class MasterPricelistFreightImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    private $successCount = 0;
    private $errorCount = 0;
    private $errors = [];
    private $rowNumber = 0;

    public function model(array $row)
    {
        $this->rowNumber++;

        try {
            $namaBarang = $this->getRowValue($row, ['nama_barang', 'barang', 'item']);
            $lokasiRaw = $this->getRowValue($row, ['lokasi', 'location']);
            $vendor = $this->getRowValue($row, ['vendor']);
            $tarifRaw = $this->getRowValue($row, ['tarif', 'price', 'biaya', 'rate']);
            $statusRaw = $this->getRowValue($row, ['status']);
            $keterangan = $this->getRowValue($row, ['keterangan', 'note', 'notes']);

            // Skip if crucial fields are empty
            if (empty($namaBarang) && empty($tarifRaw)) {
                return null;
            }

            $tarif = $this->cleanNumber($tarifRaw);
            $lokasi = $this->normalizeLokasi($lokasiRaw);
            $status = $this->normalizeStatus($statusRaw);

            if (empty($namaBarang)) {
                $this->errorCount++;
                $this->errors[] = "Baris {$this->rowNumber}: Nama Barang harus diisi";
                return null;
            }

            // Check if exists
            $existing = MasterPricelistFreight::where('nama_barang', $namaBarang)
                ->where('lokasi', $lokasi)
                ->where('vendor', $vendor)
                ->first();

            if ($existing) {
                $existing->update([
                    'tarif' => $tarif,
                    'status' => $status,
                    'keterangan' => $keterangan,
                ]);
                $this->successCount++;
                return null;
            }

            $this->successCount++;
            return new MasterPricelistFreight([
                'nama_barang' => $namaBarang,
                'lokasi' => $lokasi,
                'vendor' => $vendor,
                'tarif' => $tarif,
                'status' => $status,
                'keterangan' => $keterangan,
            ]);

        } catch (\Exception $e) {
            $this->errorCount++;
            $this->errors[] = "Baris {$this->rowNumber}: Error - " . $e->getMessage();
            return null;
        }
    }

    private function getRowValue(array $row, array $possibleKeys)
    {
        foreach ($possibleKeys as $key) {
            // Normalize key for comparison (lowercase and underscore)
            $normalizedKey = str_replace(' ', '_', strtolower($key));
            
            // Search in keys of $row
            foreach ($row as $rowKey => $rowValue) {
                if (str_replace(' ', '_', strtolower($rowKey)) === $normalizedKey) {
                    return $rowValue;
                }
            }
        }
        return '';
    }

    private function cleanNumber($value)
    {
        if ($value === null || $value === '') return 0;
        $cleaned = preg_replace('/[^\d,.-]/', '', (string)$value);
        $cleaned = str_replace(',', '.', $cleaned);
        return (float) $cleaned;
    }

    private function normalizeLokasi($value)
    {
        if (empty($value)) return null;
        $val = trim(strtolower((string) $value));
        if (in_array(ucfirst($val), ['Jakarta', 'Batam', 'Pinang'])) {
            return ucfirst($val);
        }
        return ucfirst($val);
    }

    private function normalizeStatus($value)
    {
        $val = trim(strtolower((string) $value));
        if ($val === 'aktif' || $val === 'active' || $val === '1' || $val === 'yes') {
            return 'Aktif';
        }
        return 'Tidak Aktif';
    }

    public function getSuccessCount()
    {
        return $this->successCount;
    }

    public function getErrorCount()
    {
        return $this->errorCount;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
