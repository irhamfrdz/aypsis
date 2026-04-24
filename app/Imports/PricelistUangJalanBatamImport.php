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
            $size = $this->robustGet($row, ['size', 'ukuran', 'kontainer']);
            $f_e = $this->robustGet($row, ['f/e', 'fe', 'f_e', 'full/empty', 'kondisi']);
            $tarif = $this->robustGet($row, ['tarif', 'harga', 'price', 'total']);
            $tarif_base = $this->robustGet($row, ['tarif_base', 'base_tarif', 'base_price', 'tarif_asli']);
            $tarif_antar_lokasi = $this->robustGet($row, ['tarif_antar_lokasi', 'antar_lokasi', 'biaya_antar']);
            $status = $this->robustGet($row, ['status', 'keterangan', 'ket']);
            
            // Clean data
            $expedisi = !empty($expedisi) ? trim($expedisi) : '';
            $ring = !empty($ring) ? trim($ring) : '';

            $size = !empty($size) ? trim($size) : '';
            $f_e = !empty($f_e) ? trim($f_e) : '';
            $tarif = $this->cleanTarif($tarif);
            $tarif_base = !empty($tarif_base) ? $this->cleanTarif($tarif_base) : null;
            $tarif_antar_lokasi = !empty($tarif_antar_lokasi) ? $this->cleanTarif($tarif_antar_lokasi) : 0;
            $status = !empty($status) ? trim($status) : null;

            // Skip if crucial fields are empty
            if (empty($expedisi) && empty($ring) && empty($size)) {
                return null;
            }

            // Normalize values
            if (is_numeric($size)) $size .= 'FT';
            $size = strtoupper($size);
            if (!Str::endsWith($size, 'FT')) $size .= 'FT';

            $f_e = ucfirst(strtolower($f_e));
            if ($f_e === 'F') $f_e = 'Full';
            if ($f_e === 'E') $f_e = 'Empty';

            if ($status) {
                $status = strtoupper($status);
                if (str_contains($status, 'AQUA')) $status = 'AQUA';
                if (str_contains($status, 'CHASIS')) $status = 'CHASIS PB';
            }

            // Validations
            if (empty($expedisi) || empty($ring) || empty($size) || empty($f_e)) {
                $this->errorCount++;
                $this->errors[] = "Baris {$this->rowNumber}: Data (Expedisi, Ring, Size, F/E) tidak lengkap.";
                return null;
            }

            if (!in_array($f_e, ['Full', 'Empty'])) {
                $this->errorCount++;
                $this->errors[] = "Baris {$this->rowNumber} ({$expedisi}): F/E '{$f_e}' tdk valid.";
                return null;
            }

            if (!in_array($size, ['20FT', '40FT', '45FT'])) {
                $this->errorCount++;
                $this->errors[] = "Baris {$this->rowNumber} ({$expedisi}): Size '{$size}' tdk valid (Harus 20FT, 40FT, 45FT).";
                return null;
            }

            // Check duplicate
            $exists = PricelistUangJalanBatam::where('expedisi', $expedisi)
                ->where('ring', $ring)
                ->where('size', $size)
                ->where('f_e', $f_e)
                ->first();

            if ($exists) {
                $updateData = [
                    'tarif' => $tarif,
                    'tarif_antar_lokasi' => $tarif_antar_lokasi,
                    'status' => $status ?? $exists->status,
                ];
                
                // Update data if provided in Excel

                if ($tarif_base !== null) $updateData['tarif_base'] = $tarif_base;

                $exists->update($updateData);
                $this->updatedCount++;
                return null;
            }

            // New record
            $this->addedCount++;
            return new PricelistUangJalanBatam([
                'expedisi' => $expedisi,
                'ring' => $ring,

                'size' => $size,
                'f_e' => $f_e,
                'tarif' => $tarif,
                'tarif_base' => $tarif_base ?? $tarif,
                'tarif_antar_lokasi' => $tarif_antar_lokasi,
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
