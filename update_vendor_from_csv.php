<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\DaftarTagihanKontainerSewa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Script untuk update nomor vendor berdasarkan CSV file
 *
 * Usage: php update_vendor_from_csv.php
 */

class VendorUpdater
{
    private $csvFilePath;
    private $delimiter = ';';
    private $updatedCount = 0;
    private $notFoundCount = 0;
    private $errors = [];

    public function __construct($csvFilePath = null)
    {
        // Path ke file CSV - sesuaikan dengan lokasi file
        $this->csvFilePath = $csvFilePath ?: 'C:\Users\amanda\Downloads\Zona.csv';

        if (!file_exists($this->csvFilePath)) {
            throw new \Exception("File CSV tidak ditemukan: {$this->csvFilePath}");
        }
    }

    /**
     * Membaca dan memproses CSV file
     */
    public function processCsv()
    {
        echo "====================================================\n";
        echo "SCRIPT UPDATE VENDOR INVOICE DARI CSV\n";
        echo "====================================================\n";
        echo "File CSV: {$this->csvFilePath}\n";
        echo "Waktu mulai: " . now()->format('Y-m-d H:i:s') . "\n\n";

        // Buka file CSV
        $file = fopen($this->csvFilePath, 'r');
        if (!$file) {
            throw new \Exception("Tidak dapat membuka file CSV");
        }

        // Baca header
        $headers = fgetcsv($file, 0, $this->delimiter);
        if (!$headers) {
            fclose($file);
            throw new \Exception("Tidak dapat membaca header CSV");
        }

        echo "Header CSV ditemukan:\n";
        foreach ($headers as $index => $header) {
            echo "  [{$index}] {$header}\n";
        }
        echo "\n";

        // Mapping kolom yang diperlukan
        $kontainerColumn = $this->findColumnIndex($headers, ['Kontainer', 'kontainer', 'nomor_kontainer']);
        $invoiceVendorColumn = $this->findColumnIndex($headers, ['No.InvoiceVendor', 'invoice_vendor', 'Invoice Vendor']);
        $tanggalVendorColumn = $this->findColumnIndex($headers, ['Tgl.InvVendor', 'tanggal_vendor', 'Tanggal Vendor']);

        if ($kontainerColumn === false) {
            fclose($file);
            throw new \Exception("Kolom kontainer tidak ditemukan dalam CSV");
        }

        if ($invoiceVendorColumn === false) {
            fclose($file);
            throw new \Exception("Kolom invoice vendor tidak ditemukan dalam CSV");
        }

        echo "Mapping kolom:\n";
        echo "  - Kontainer: kolom [{$kontainerColumn}] {$headers[$kontainerColumn]}\n";
        echo "  - Invoice Vendor: kolom [{$invoiceVendorColumn}] {$headers[$invoiceVendorColumn]}\n";
        if ($tanggalVendorColumn !== false) {
            echo "  - Tanggal Vendor: kolom [{$tanggalVendorColumn}] {$headers[$tanggalVendorColumn]}\n";
        }
        echo "\n";

        // Mulai transaksi database
        DB::beginTransaction();

        try {
            $rowNumber = 1;

            // Proses setiap baris
            while (($row = fgetcsv($file, 0, $this->delimiter)) !== false) {
                $rowNumber++;

                // Skip baris kosong atau baris dengan data tidak lengkap
                if (count($row) < max($kontainerColumn, $invoiceVendorColumn) + 1) {
                    continue;
                }

                $kontainer = trim($row[$kontainerColumn] ?? '');
                $invoiceVendor = trim($row[$invoiceVendorColumn] ?? '');
                $tanggalVendor = null;

                // Parse tanggal vendor jika ada
                if ($tanggalVendorColumn !== false && !empty($row[$tanggalVendorColumn])) {
                    $tanggalVendor = $this->parseDate(trim($row[$tanggalVendorColumn]));
                }

                // Skip jika data kontainer atau invoice vendor kosong
                if (empty($kontainer) || empty($invoiceVendor)) {
                    continue;
                }

                // Update database
                $this->updateVendorInfo($kontainer, $invoiceVendor, $tanggalVendor, $rowNumber);
            }

            fclose($file);

            // Commit transaksi
            DB::commit();

            // Tampilkan hasil
            $this->showResults();

        } catch (\Exception $e) {
            DB::rollback();
            fclose($file);
            throw $e;
        }
    }

    /**
     * Mencari index kolom berdasarkan nama-nama yang mungkin
     */
    private function findColumnIndex($headers, $possibleNames)
    {
        foreach ($possibleNames as $name) {
            $index = array_search($name, $headers);
            if ($index !== false) {
                return $index;
            }
        }
        return false;
    }

    /**
     * Parse tanggal dari berbagai format
     */
    private function parseDate($dateString)
    {
        if (empty($dateString)) {
            return null;
        }

        $formats = [
            'd M y',        // 13 Jul 23
            'd-M-y',        // 13-Jul-23
            'd/m/Y',        // 13/07/2023
            'd-m-Y',        // 13-07-2023
            'Y-m-d',        // 2023-07-13
            'd M Y',        // 13 Jul 2023
        ];

        foreach ($formats as $format) {
            try {
                $date = Carbon::createFromFormat($format, $dateString);
                if ($date) {
                    return $date->format('Y-m-d');
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return null;
    }

    /**
     * Update informasi vendor di database
     */
    private function updateVendorInfo($kontainer, $invoiceVendor, $tanggalVendor, $rowNumber)
    {
        try {
            // Cari record berdasarkan nomor kontainer
            $records = DaftarTagihanKontainerSewa::where('nomor_kontainer', $kontainer)->get();

            if ($records->isEmpty()) {
                $this->notFoundCount++;
                $this->errors[] = "Baris {$rowNumber}: Kontainer '{$kontainer}' tidak ditemukan di database";
                echo "⚠️  Baris {$rowNumber}: Kontainer '{$kontainer}' tidak ditemukan\n";
                return;
            }

            // Update semua record yang cocok
            $updateData = ['invoice_vendor' => $invoiceVendor];
            if ($tanggalVendor) {
                $updateData['tanggal_vendor'] = $tanggalVendor;
            }

            $updatedRecords = DaftarTagihanKontainerSewa::where('nomor_kontainer', $kontainer)
                ->update($updateData);

            $this->updatedCount += $updatedRecords;

            echo "✅ Baris {$rowNumber}: Updated {$updatedRecords} record(s) untuk kontainer '{$kontainer}' dengan invoice '{$invoiceVendor}'";
            if ($tanggalVendor) {
                echo " tanggal {$tanggalVendor}";
            }
            echo "\n";

        } catch (\Exception $e) {
            $this->errors[] = "Baris {$rowNumber}: Error updating kontainer '{$kontainer}' - " . $e->getMessage();
            echo "❌ Baris {$rowNumber}: Error - " . $e->getMessage() . "\n";
        }
    }

    /**
     * Tampilkan hasil pemrosesan
     */
    private function showResults()
    {
        echo "\n====================================================\n";
        echo "HASIL PEMROSESAN\n";
        echo "====================================================\n";
        echo "✅ Total record berhasil diupdate: {$this->updatedCount}\n";
        echo "⚠️  Total kontainer tidak ditemukan: {$this->notFoundCount}\n";
        echo "❌ Total error: " . count($this->errors) . "\n";
        echo "Waktu selesai: " . now()->format('Y-m-d H:i:s') . "\n";

        if (!empty($this->errors)) {
            echo "\nDETAIL ERROR:\n";
            foreach ($this->errors as $error) {
                echo "  - {$error}\n";
            }
        }

        echo "\n====================================================\n";

        // Log hasil ke file
        $logMessage = "Vendor update completed. Updated: {$this->updatedCount}, Not found: {$this->notFoundCount}, Errors: " . count($this->errors);
        Log::info($logMessage);
    }
}

// Jalankan script
try {
    // Inisialisasi Laravel app
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    // Jalankan updater
    $updater = new VendorUpdater();
    $updater->processCsv();

} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
