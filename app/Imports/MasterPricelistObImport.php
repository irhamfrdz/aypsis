<?php

namespace App\Imports;

use App\Models\MasterPricelistOb;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MasterPricelistObImport implements SkipsEmptyRows, ToModel, WithCustomCsvSettings, WithHeadingRow
{
    private $successCount = 0;

    private $errorCount = 0;

    private $errors = [];

    private $rowNumber = 0;

    public function model(array $row)
    {
        $this->rowNumber++;

        try {
            $sizeRaw = $this->getRowValue($row, ['size_kontainer', 'size', 'ukuran', 'ukuran_kontainer']);
            $statusRaw = $this->getRowValue($row, ['status_kontainer', 'status']);
            $statusServiceRaw = $this->getRowValue($row, ['status_service', 'service']);
            $biayaRaw = $this->getRowValue($row, ['biaya', 'cost', 'harga']);
            $keterangan = $this->getRowValue($row, ['keterangan', 'note', 'notes']);

            // Skip if all required fields are empty
            if (empty($sizeRaw) && empty($statusRaw) && empty($biayaRaw)) {
                return null;
            }

            $size = $this->normalizeSize($sizeRaw);
            $status = $this->normalizeStatus($statusRaw);
            $statusService = $this->normalizeStatusService($statusServiceRaw ?: 'non_service');
            $biaya = $this->cleanNumber($biayaRaw);

            if (empty($size) || empty($status) || empty($statusService) || $biaya <= 0) {
                $this->errorCount++;
                $this->errors[] = "Baris {$this->rowNumber}: Data tidak lengkap atau tidak valid";

                return null;
            }

            // Update existing or create new
            $exists = MasterPricelistOb::where('size_kontainer', $size)
                ->where('status_kontainer', $status)
                ->where('status_service', $statusService)
                ->first();

            if ($exists) {
                $exists->update([
                    'status_service' => $statusService,
                    'biaya' => $biaya,
                    'keterangan' => $keterangan,
                ]);
                $this->successCount++;

                return null;
            }

            $this->successCount++;

            return new MasterPricelistOb([
                'size_kontainer' => $size,
                'status_kontainer' => $status,
                'status_service' => $statusService,
                'biaya' => $biaya,
                'keterangan' => $keterangan,
            ]);

        } catch (\Exception $e) {
            $this->errorCount++;
            $this->errors[] = "Baris {$this->rowNumber}: Error - ".$e->getMessage();

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

    private function normalizeSize($size)
    {
        if ($size === null) {
            return null;
        }
        $s = strtoupper(trim((string) $size));
        // Accept '20', '20ft', '20 FT', '20 FT'
        if (preg_match('/^20/', $s)) {
            return '20ft';
        }
        if (preg_match('/^40/', $s)) {
            return '40ft';
        }

        return null;
    }

    private function normalizeStatus($status)
    {
        if ($status === null) {
            return null;
        }
        $s = strtolower(trim((string) $status));
        if (in_array($s, ['full', 'f', '0'])) {
            return 'full';
        }
        if (in_array($s, ['empty', 'e', '1'])) {
            return 'empty';
        }

        return null;
    }

    private function normalizeStatusService($statusService)
    {
        $s = strtolower(trim((string) $statusService));

        if (in_array($s, ['service', 'servis', 'yes', 'ya', '1'])) {
            return 'service';
        }
        if (in_array($s, ['non_service', 'non-service', 'bukan service', 'bukan servis', 'no', 'tidak', '0'])) {
            return 'non_service';
        }

        return null;
    }

    private function cleanNumber($value)
    {
        if ($value === null || $value === '') {
            return 0;
        }
        $val = trim((string) $value);
        // Remove spaces
        $val = str_replace(' ', '', $val);
        // If european format 170.500,00 -> turn into 170500.00
        if (preg_match('/^[\d.]+,\d+$/', $val)) {
            $val = str_replace('.', '', $val);
            $val = str_replace(',', '.', $val);
        } else {
            // Remove thousands separators
            $parts = explode('.', $val);
            if (count($parts) > 2) {
                $val = str_replace('.', '', $val);
            } elseif (count($parts) == 2 && strlen($parts[1]) == 3) {
                $val = str_replace('.', '', $val);
            }
            $val = str_replace(',', '', $val);
        }

        return floatval($val);
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function getErrorCount(): int
    {
        return $this->errorCount;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Use semicolon as CSV delimiter by default – matches project CSV templates
     */
    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';',
        ];
    }
}
