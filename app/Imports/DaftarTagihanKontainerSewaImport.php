<?php

namespace App\Imports;

use App\Models\DaftarTagihanKontainerSewa;
use App\Models\MasterPricelistSewaKontainer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DaftarTagihanKontainerSewaImport implements ToCollection, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading
{
    private $importResults = [];
    private $errors = [];
    private $warnings = [];
    private $imported_count = 0;
    private $updated_count = 0;
    private $skipped_count = 0;
    private $validate_only = false;
    private $skip_duplicates = true;
    private $update_existing = false;

    public function __construct($options = [])
    {
        $this->validate_only = $options['validate_only'] ?? false;
        $this->skip_duplicates = $options['skip_duplicates'] ?? true;
        $this->update_existing = $options['update_existing'] ?? false;
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 because of 0-based index and header row

            try {
                // Skip empty rows
                if ($this->isEmptyRow($row)) {
                    continue;
                }

                // Clean and validate data
                $cleanedData = $this->cleanRowData($row->toArray());

                // Check for duplicates
                $existing = $this->findExistingRecord($cleanedData);

                if ($existing) {
                    if ($this->skip_duplicates && !$this->update_existing) {
                        $this->skipped_count++;
                        $this->warnings[] = "Baris {$rowNumber}: Data sudah ada (Kontainer: {$cleanedData['nomor_kontainer']}, Periode: {$cleanedData['periode']}) - diskip";
                        continue;
                    } elseif ($this->update_existing) {
                        $this->updateRecord($existing, $cleanedData, $rowNumber);
                        continue;
                    }
                }

                // Calculate financial data
                $financialData = $this->calculateFinancialData($cleanedData);
                $cleanedData = array_merge($cleanedData, $financialData);

                // Validate business rules
                $this->validateBusinessRules($cleanedData, $rowNumber);

                // If validation only, don't save
                if ($this->validate_only) {
                    $this->imported_count++;
                    continue;
                }

                // Create new record
                $tagihan = DaftarTagihanKontainerSewa::create($cleanedData);
                $this->imported_count++;

                Log::info("Import: Created tagihan", [
                    'id' => $tagihan->id,
                    'nomor_kontainer' => $tagihan->nomor_kontainer,
                    'periode' => $tagihan->periode
                ]);

            } catch (\Exception $e) {
                $this->errors[] = [
                    'row' => $rowNumber,
                    'message' => $e->getMessage(),
                    'data' => $row->toArray()
                ];
                Log::error("Import error on row {$rowNumber}: " . $e->getMessage(), [
                    'row_data' => $row->toArray(),
                    'exception' => $e
                ]);
            }
        }
    }

    /**
     * Check if row is empty
     */
    private function isEmptyRow($row): bool
    {
        $requiredFields = ['vendor', 'nomor_kontainer', 'size', 'tanggal_awal', 'tanggal_akhir'];

        foreach ($requiredFields as $field) {
            if (!empty($row[$field])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Clean and standardize row data
     */
    private function cleanRowData(array $row): array
    {
        // Map and clean the data
        $cleaned = [
            'vendor' => $this->cleanVendor($row['vendor'] ?? ''),
            'nomor_kontainer' => strtoupper(trim($row['nomor_kontainer'] ?? '')),
            'size' => $this->cleanSize($row['size'] ?? ''),
            'tanggal_awal' => $this->parseDate($row['tanggal_awal'] ?? ''),
            'tanggal_akhir' => $this->parseDate($row['tanggal_akhir'] ?? ''),
            'tarif' => $this->cleanNumber($row['tarif'] ?? 0),
            'group' => trim($row['group'] ?? ''),
            'status' => $this->cleanStatus($row['status'] ?? 'ongoing'),
            'status_pranota' => null, // Default null for new records
            'pranota_id' => null,
        ];

        // Calculate periode and masa
        if ($cleaned['tanggal_awal'] && $cleaned['tanggal_akhir']) {
            $startDate = Carbon::parse($cleaned['tanggal_awal']);
            $endDate = Carbon::parse($cleaned['tanggal_akhir']);

            $cleaned['periode'] = $startDate->diffInDays($endDate) + 1; // +1 to include both start and end dates
            $cleaned['masa'] = $cleaned['periode'] . ' Hari';
        }

        // Remove empty group
        if (empty($cleaned['group']) || $cleaned['group'] === '-') {
            $cleaned['group'] = null;
        }

        return $cleaned;
    }

    /**
     * Clean vendor name
     */
    private function cleanVendor(string $vendor): string
    {
        $vendor = strtoupper(trim($vendor));

        // Standardize vendor names
        if (in_array($vendor, ['ZONA', 'PT ZONA', 'PT. ZONA'])) {
            return 'ZONA';
        }

        if (in_array($vendor, ['DPE', 'PT DPE', 'PT. DPE'])) {
            return 'DPE';
        }

        return $vendor;
    }

    /**
     * Clean container size
     */
    private function cleanSize($size): string
    {
        $size = trim($size);

        // Handle various size formats
        if (in_array($size, ['20', '20ft', '20 ft', '20\''])) {
            return '20';
        }

        if (in_array($size, ['40', '40ft', '40 ft', '40\''])) {
            return '40';
        }

        return (string) $size;
    }

    /**
     * Clean status
     */
    private function cleanStatus(string $status): string
    {
        $status = strtolower(trim($status));

        if (in_array($status, ['ongoing', 'active', 'aktif', 'berjalan'])) {
            return 'ongoing';
        }

        if (in_array($status, ['selesai', 'completed', 'done', 'finished'])) {
            return 'selesai';
        }

        return 'ongoing'; // Default
    }

    /**
     * Parse date from various formats
     */
    private function parseDate($date)
    {
        if (empty($date)) {
            return null;
        }

        try {
            // Handle Excel serial date
            if (is_numeric($date)) {
                return Carbon::createFromFormat('Y-m-d', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date)->format('Y-m-d'));
            }

            // Handle various date formats
            $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'm/d/Y', 'Y/m/d'];

            foreach ($formats as $format) {
                try {
                    return Carbon::createFromFormat($format, $date)->format('Y-m-d');
                } catch (\Exception $e) {
                    continue;
                }
            }

            // Try Carbon parse as last resort
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            throw new \Exception("Format tanggal tidak valid: {$date}");
        }
    }

    /**
     * Clean numeric values
     */
    private function cleanNumber($value): float
    {
        if (empty($value)) {
            return 0;
        }

        // Remove currency symbols and formatting
        $cleaned = preg_replace('/[^\d.,\-]/', '', $value);
        $cleaned = str_replace(',', '.', $cleaned);

        return (float) $cleaned;
    }

    /**
     * Find existing record
     */
    private function findExistingRecord(array $data)
    {
        return DaftarTagihanKontainerSewa::where('nomor_kontainer', $data['nomor_kontainer'])
            ->where('periode', $data['periode'])
            ->first();
    }

    /**
     * Update existing record
     */
    private function updateRecord($existing, array $data, int $rowNumber)
    {
        if ($this->validate_only) {
            $this->updated_count++;
            return;
        }

        $existing->update($data);
        $this->updated_count++;

        Log::info("Import: Updated tagihan", [
            'id' => $existing->id,
            'nomor_kontainer' => $existing->nomor_kontainer,
            'periode' => $existing->periode
        ]);
    }

    /**
     * Calculate financial data (DPP, PPN, PPH, Grand Total)
     */
    private function calculateFinancialData(array $data): array
    {
        $tarif = $data['tarif'];
        $periode = $data['periode'];
        $size = $data['size'];
        $vendor = $data['vendor'];

        // Get master pricelist if available
        $masterPricelist = MasterPricelistSewaKontainer::where('size', $size)
            ->where('vendor', $vendor)
            ->first();

        // If master pricelist exists and no tarif provided, use master tarif
        if ($masterPricelist && $tarif == 0) {
            $tarif = $masterPricelist->tarif;
        }

        // Calculate DPP (tarif * periode)
        $dpp = $tarif * $periode;

        // Calculate PPN (11% of DPP)
        $ppn = $dpp * 0.11;

        // Calculate PPH (2% of DPP)
        $pph = $dpp * 0.02;

        // Grand Total = DPP + PPN - PPH
        $grand_total = $dpp + $ppn - $pph;

        return [
            'tarif' => $tarif,
            'dpp' => $dpp,
            'adjustment' => 0, // Default adjustment
            'dpp_nilai_lain' => 0, // Default nilai lain
            'ppn' => $ppn,
            'pph' => $pph,
            'grand_total' => $grand_total,
        ];
    }

    /**
     * Validate business rules
     */
    private function validateBusinessRules(array $data, int $rowNumber)
    {
        // Check if vendor is supported
        if (!in_array($data['vendor'], ['ZONA', 'DPE'])) {
            throw new \Exception("Vendor tidak didukung: {$data['vendor']}. Harus ZONA atau DPE");
        }

        // Check if size is valid
        if (!in_array($data['size'], ['20', '40'])) {
            throw new \Exception("Ukuran kontainer tidak valid: {$data['size']}. Harus 20 atau 40");
        }

        // Check date logic
        if ($data['tanggal_awal'] && $data['tanggal_akhir']) {
            $startDate = Carbon::parse($data['tanggal_awal']);
            $endDate = Carbon::parse($data['tanggal_akhir']);

            if ($endDate->lt($startDate)) {
                throw new \Exception("Tanggal akhir tidak boleh lebih awal dari tanggal awal");
            }

            // Check if periode is reasonable (not more than 1 year)
            if ($data['periode'] > 365) {
                $this->warnings[] = "Baris {$rowNumber}: Periode terlalu lama ({$data['periode']} hari). Periksa tanggal.";
            }
        }

        // Check tarif
        if ($data['tarif'] <= 0) {
            throw new \Exception("Tarif harus lebih besar dari 0");
        }

        // Check container number format
        if (strlen($data['nomor_kontainer']) < 4) {
            throw new \Exception("Nomor kontainer terlalu pendek: {$data['nomor_kontainer']}");
        }
    }

    /**
     * Get import results
     */
    public function getResults(): array
    {
        return [
            'success' => empty($this->errors),
            'imported_count' => $this->imported_count,
            'updated_count' => $this->updated_count,
            'skipped_count' => $this->skipped_count,
            'total_processed' => $this->imported_count + $this->updated_count + $this->skipped_count,
            'errors' => $this->errors,
            'warnings' => $this->warnings,
            'validate_only' => $this->validate_only,
        ];
    }

    /**
     * Laravel Excel validation rules
     */
    public function rules(): array
    {
        return [
            'vendor' => 'required|string|max:50',
            'nomor_kontainer' => 'required|string|max:100',
            'size' => 'required|string|max:10',
            'tanggal_awal' => 'required',
            'tanggal_akhir' => 'required',
            'tarif' => 'nullable|numeric|min:0',
            'group' => 'nullable|string|max:100',
            'status' => 'nullable|string|max:20',
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'vendor.required' => 'Vendor wajib diisi',
            'nomor_kontainer.required' => 'Nomor kontainer wajib diisi',
            'size.required' => 'Ukuran kontainer wajib diisi',
            'tanggal_awal.required' => 'Tanggal awal wajib diisi',
            'tanggal_akhir.required' => 'Tanggal akhir wajib diisi',
            'tarif.numeric' => 'Tarif harus berupa angka',
            'tarif.min' => 'Tarif tidak boleh negatif',
        ];
    }

    /**
     * Batch size for processing
     */
    public function batchSize(): int
    {
        return 100;
    }

    /**
     * Chunk size for reading
     */
    public function chunkSize(): int
    {
        return 100;
    }
}
