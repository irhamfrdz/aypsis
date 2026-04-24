<?php

namespace App\Imports;

use App\Models\PricelistUangJalanBatam;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PricelistUangJalanBatamImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    private $addedCount = 0;
    private $updatedCount = 0;
    private $errorCount = 0;
    private $errors = [];
    private $rowNumber = 0;
    private $headers = null;

    /**
     * Transform each row into a model
     */
    public function model(array $row)
    {
        $this->rowNumber = ($this->rowNumber ?: 0) + 1;
        
        try {
            // Get all available headers from the row for matching
            if ($this->headers === null) {
                $this->headers = array_keys($row);
            }

            // More robust column matching
            $expedisi = $this->robustGet($row, ['expedisi', 'expe', 'vendor']);
            $ring = $this->robustGet($row, ['ring', 'wilayah', 'area']);
            $tarif_20ft_full = $this->robustGet($row, ['tarif_20ft_full', '20ft_full', '20ft full', '20_full']);
            $tarif_20ft_empty = $this->robustGet($row, ['tarif_20ft_empty', '20ft_empty', '20ft empty', '20_empty']);
            $tarif_40ft_full = $this->robustGet($row, ['tarif_40ft_full', '40ft_full', '40ft full', '40_full']);
            $tarif_40ft_empty = $this->robustGet($row, ['tarif_40ft_empty', '40ft_empty', '40ft empty', '40_empty']);
            $tarif_antarlokasi_20ft = $this->robustGet($row, ['tarif_antarlokasi_20ft', 'antarlokasi_20ft', 'al_20ft', 'al 20ft']);
            $tarif_antarlokasi_40ft = $this->robustGet($row, ['tarif_antarlokasi_40ft', 'antarlokasi_40ft', 'al_40ft', 'al 40ft']);
            $status = $this->robustGet($row, ['status', 'keterangan', 'ket']);
            
            // Clean data
            $expedisi = !empty($expedisi) ? trim($expedisi) : '';
            $ring = !empty($ring) ? trim($ring) : '';
            $tarif_20ft_full = !empty($tarif_20ft_full) ? $this->cleanTarif($tarif_20ft_full) : 0;
            $tarif_20ft_empty = !empty($tarif_20ft_empty) ? $this->cleanTarif($tarif_20ft_empty) : 0;
            $tarif_40ft_full = !empty($tarif_40ft_full) ? $this->cleanTarif($tarif_40ft_full) : 0;
            $tarif_40ft_empty = !empty($tarif_40ft_empty) ? $this->cleanTarif($tarif_40ft_empty) : 0;
            $tarif_antarlokasi_20ft = !empty($tarif_antarlokasi_20ft) ? $this->cleanTarif($tarif_antarlokasi_20ft) : 0;
            $tarif_antarlokasi_40ft = !empty($tarif_antarlokasi_40ft) ? $this->cleanTarif($tarif_antarlokasi_40ft) : 0;
            $status = !empty($status) ? trim($status) : null;

            // Skip if crucial fields are empty
            if (empty($expedisi) && empty($ring)) {
                return null;
            }

            // Normalize values




            if ($status) {
                $status = strtoupper($status);
                if (str_contains($status, 'AQUA')) $status = 'AQUA';
                if (str_contains($status, 'CHASIS')) $status = 'CHASIS PB';
            }

            // Validations
            if (empty($expedisi) || empty($ring)) {
                $this->errorCount++;
                $this->errors[] = "Baris {$this->rowNumber}: Data (Expedisi, Ring) tidak lengkap.";
                return null;
            }





            // Check duplicate
            $exists = PricelistUangJalanBatam::where('expedisi', $expedisi)
                ->where('ring', $ring)
                ->first();

            if ($exists) {
                $exists->update([
                    'tarif_20ft_full' => $tarif_20ft_full,
                    'tarif_20ft_full_base' => $tarif_20ft_full,
                    'tarif_20ft_empty' => $tarif_20ft_empty,
                    'tarif_20ft_empty_base' => $tarif_20ft_empty,
                    'tarif_40ft_full' => $tarif_40ft_full,
                    'tarif_40ft_full_base' => $tarif_40ft_full,
                    'tarif_40ft_empty' => $tarif_40ft_empty,
                    'tarif_40ft_empty_base' => $tarif_40ft_empty,
                    'tarif_antarlokasi_20ft' => $tarif_antarlokasi_20ft,
                    'tarif_antarlokasi_40ft' => $tarif_antarlokasi_40ft,
                    'status' => $status ?? $exists->status,
                ]);
                $this->updatedCount++;
                return null;
            }

            // New record
            $this->addedCount++;
            return new PricelistUangJalanBatam([
                'expedisi' => $expedisi,
                'ring' => $ring,
                'tarif_20ft_full' => $tarif_20ft_full,
                'tarif_20ft_full_base' => $tarif_20ft_full,
                'tarif_20ft_empty' => $tarif_20ft_empty,
                'tarif_20ft_empty_base' => $tarif_20ft_empty,
                'tarif_40ft_full' => $tarif_40ft_full,
                'tarif_40ft_full_base' => $tarif_40ft_full,
                'tarif_40ft_empty' => $tarif_40ft_empty,
                'tarif_40ft_empty_base' => $tarif_40ft_empty,
                'tarif_antarlokasi_20ft' => $tarif_antarlokasi_20ft,
                'tarif_antarlokasi_40ft' => $tarif_antarlokasi_40ft,
                'status' => $status,
            ]);

        } catch (\Exception $e) {
            $this->errorCount++;
            $this->errors[] = "Baris {$this->rowNumber}: " . $e->getMessage();
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
        return $this->addedCount + $this->updatedCount;
    }

    public function getAddedCount(): int
    {
        return $this->addedCount;
    }

    public function getUpdatedCount(): int
    {
        return $this->updatedCount;
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
     * More robust value retrieval by searching for partial header matches
     */
    private function robustGet(array $row, array $tags)
    {
        $keys = array_keys($row);
        
        // 1. Precise match (slugified by WithHeadingRow)
        foreach ($tags as $tag) {
            // Slugify tag to match Maatwebsite's likely key
            $slugTag = Str::slug($tag, '_');
            if (isset($row[$slugTag]) && $row[$slugTag] !== '') {
                return $row[$slugTag];
            }
        }
        
        // 2. Fuzzy search (if header row had spaces or different symbols)
        foreach ($keys as $key) {
            $cleanKey = strtolower(preg_replace('/[^a-z0-9]/', '', $key));
            foreach ($tags as $tag) {
                $cleanTag = strtolower(preg_replace('/[^a-z0-9]/', '', $tag));
                if ($cleanKey === $cleanTag || str_contains($cleanKey, $cleanTag)) {
                    if ($row[$key] !== '') {
                        return $row[$key];
                    }
                }
            }
        }
        
        return '';
    }

    /**
     * Get row value by trying multiple possible keys (DEPRECATED: use robustGet)
     */
    private function getRowValue(array $row, array $possibleKeys)
    {
        return $this->robustGet($row, $possibleKeys);
    }

}
