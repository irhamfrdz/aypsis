<?php

namespace App\Imports;

use App\Models\MasterPricelistAirTawar;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Illuminate\Support\Facades\Log;

class MasterPricelistAirTawarImport implements ToModel, WithHeadingRow, SkipsEmptyRows, WithCustomCsvSettings
{
    private $successCount = 0;
    private $errorCount = 0;
    private $errors = [];
    private $rowNumber = 0;

    public function model(array $row)
    {
        $this->rowNumber++;

        try {
            $namaAgen = $this->getRowValue($row, ['nama_agen', 'agen', 'nama']);
            $hargaRaw = $this->getRowValue($row, ['harga', 'price', 'biaya']);
            $keterangan = $this->getRowValue($row, ['keterangan', 'note', 'notes']);

            // Skip if all required fields are empty
            if (empty($namaAgen) && empty($hargaRaw)) {
                return null;
            }

            $harga = $this->cleanNumber($hargaRaw);

            if (empty($namaAgen) || $harga <= 0) {
                $this->errorCount++;
                $this->errors[] = "Baris {$this->rowNumber}: Data tidak lengkap atau tidak valid";
                return null;
            }

            // Update existing or create new
            $exists = MasterPricelistAirTawar::where('nama_agen', $namaAgen)->first();

            if ($exists) {
                $exists->update([
                    'harga' => $harga,
                    'keterangan' => $keterangan,
                ]);
                $this->successCount++;
                return null;
            }

            $this->successCount++;
            return new MasterPricelistAirTawar([
                'nama_agen' => $namaAgen,
                'harga' => $harga,
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
            if (array_key_exists($key, $row) && $row[$key] !== '') {
                return $row[$key];
            }
        }
        return '';
    }

    private function cleanNumber($value)
    {
        if ($value === null || $value === '') return 0;
        // Remove Rp, dots, commas, spaces
        $cleaned = preg_replace('/[^\d,.-]/', '', (string)$value);
        // Handle comma as decimal separator
        $cleaned = str_replace(',', '.', $cleaned);
        return (float) $cleaned;
    }

    public function getCsvSettings(): array
    {
        return [
            'input_encoding' => 'UTF-8',
            'delimiter' => ';'
        ];
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
