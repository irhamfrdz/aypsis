<?php

namespace App\Imports;

use App\Models\PricelistUangJalanBatam;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Facades\Log;

class PricelistUangJalanBatamImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    private $successCount = 0;
    private $errorCount = 0;
    private $errors = [];
    private $rowNumber = 0;

    /**
     * Transform each row into a model
     */
    public function model(array $row)
    {
        $this->rowNumber++;
        
        try {
            // Try different possible key names (case insensitive)
            $expedisi = $this->getRowValue($row, ['expedisi', 'Expedisi', 'EXPEDISI']);
            $ring = $this->getRowValue($row, ['ring', 'Ring', 'RING']);
            $size = $this->getRowValue($row, ['size', 'Size', 'SIZE']);
            $f_e = $this->getRowValue($row, ['f_e', 'fe', 'F/E', 'f/e', 'FE']);
            $tarif = $this->getRowValue($row, ['tarif', 'Tarif', 'TARIF']);
            $status = $this->getRowValue($row, ['status', 'Status', 'STATUS']);
            
            // Skip if all required fields are empty
            if (empty($expedisi) && empty($ring) && empty($size)) {
                return null;
            }

            // Clean and validate data
            $expedisi = trim($expedisi);
            $ring = trim($ring);
            $size = trim($size);
            $f_e = trim($f_e);
            $tarif = $this->cleanTarif($tarif);
            $status = !empty($status) ? trim($status) : null;

            // Normalize Size - add FT if missing
            if (is_numeric($size)) {
                $size = $size . 'FT';
            }
            $size = strtoupper($size);

            // Normalize F/E values
            $f_e = ucfirst(strtolower($f_e)); // Full or Empty

            // Normalize Status to uppercase
            if ($status) {
                $status = strtoupper($status);
            }

            // Validate F/E
            if (!in_array($f_e, ['Full', 'Empty'])) {
                $this->errorCount++;
                $this->errors[] = "Baris dengan expedisi {$expedisi}: F/E harus Full atau Empty, ditemukan '{$f_e}'";
                return null;
            }

            // Validate Size
            if (!in_array($size, ['20FT', '40FT', '45FT'])) {
                $this->errorCount++;
                $this->errors[] = "Baris dengan expedisi {$expedisi}: Size harus 20FT, 40FT, atau 45FT, ditemukan '{$size}'";
                return null;
            }

            // Validate Status if provided
            if ($status && !in_array($status, ['AQUA', 'CHASIS PB'])) {
                $this->errorCount++;
                $this->errors[] = "Baris {$this->rowNumber} (Expedisi: {$expedisi}): Status harus AQUA atau CHASIS PB, ditemukan '{$status}'";
                return null;
            }

            // Validate required fields have values
            if (empty($expedisi) || empty($ring) || empty($size) || empty($f_e) || $tarif <= 0) {
                $this->errorCount++;
                $this->errors[] = "Baris {$this->rowNumber}: Data tidak lengkap (Expedisi: '{$expedisi}', Ring: '{$ring}', Size: '{$size}', F/E: '{$f_e}', Tarif: '{$tarif}')";
                return null;
            }

            // Check for duplicates
            $exists = PricelistUangJalanBatam::where('expedisi', $expedisi)
                ->where('ring', $ring)
                ->where('size', $size)
                ->where('f_e', $f_e)
                ->first();

            if ($exists) {
                // Update existing record
                $exists->update([
                    'tarif' => $tarif,
                    'status' => $status,
                ]);
                $this->successCount++;
                return null;
            }

            // Create new record
            $this->successCount++;
            return new PricelistUangJalanBatam([
                'expedisi' => $expedisi,
                'ring' => $ring,
                'size' => $size,
                'f_e' => $f_e,
                'tarif' => $tarif,
                'status' => $status,
            ]);

        } catch (\Exception $e) {
            $this->errorCount++;
            $this->errors[] = "Baris {$this->rowNumber}: Error - " . $e->getMessage();
            return null;
        }
    }

    /**
     * Clean tarif value (remove dots, commas, spaces, etc.)
     * Handles both formats:
     * - International: 170,500.00 or 170500
     * - European: 170.500,00 or 170 500,00
     */
    private function cleanTarif($tarif)
    {
        $tarif = trim($tarif);
        
        // Remove any spaces
        $tarif = str_replace(' ', '', $tarif);
        
        // Check if European format (comma as decimal separator)
        // European format: 170.500,00 or 1.280.812,00
        if (preg_match('/^[\d.]+,\d+$/', $tarif)) {
            // Remove dots (thousands separator)
            $tarif = str_replace('.', '', $tarif);
            // Replace comma with dot (decimal separator)
            $tarif = str_replace(',', '.', $tarif);
        } else {
            // International format or plain number
            // Remove dots if used as thousands separator (e.g., 170.500 with no decimal)
            // Keep dots if used as decimal separator (e.g., 170500.00)
            $parts = explode('.', $tarif);
            if (count($parts) > 2) {
                // Multiple dots, so dots are thousands separators
                $tarif = str_replace('.', '', $tarif);
            } elseif (count($parts) == 2 && strlen($parts[1]) == 3) {
                // One dot with 3 digits after = thousands separator (e.g., 170.500)
                $tarif = str_replace('.', '', $tarif);
            }
            // Remove commas (thousands separator in international format)
            $tarif = str_replace(',', '', $tarif);
        }
        
        // Convert to float
        return floatval($tarif);
    }

    /**
     * Get success count
     */
    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    /**
     * Get error count
     */
    public function getErrorCount(): int
    {
        return $this->errorCount;
    }

    /**
     * Get all errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get row value by trying multiple possible keys
     */
    private function getRowValue(array $row, array $possibleKeys)
    {
        foreach ($possibleKeys as $key) {
            if (isset($row[$key]) && $row[$key] !== '') {
                return $row[$key];
            }
        }
        return '';
    }

}
