<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DaftarTagihanKontainerSewa;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UpdateVendorFromCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vendor:update-from-csv {file? : Path to CSV file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update vendor invoice information from CSV file';

    private $updatedCount = 0;
    private $notFoundCount = 0;
    private $errors = [];
    private $delimiter = ';';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $csvFilePath = $this->argument('file');

        // If no file specified, try to find default locations
        if (!$csvFilePath) {
            $defaultPaths = [
                // Windows paths
                'C:\Users\amanda\Downloads\Zona.csv',
                // Linux/Server paths
                '/var/www/aypsis/storage/app/Zona.csv',
                '/var/www/aypsis/Zona.csv',
                '/tmp/Zona.csv',
                // Current directory
                getcwd() . '/Zona.csv',
                storage_path('app/Zona.csv'),
            ];

            foreach ($defaultPaths as $path) {
                if (file_exists($path)) {
                    $csvFilePath = $path;
                    break;
                }
            }

            if (!$csvFilePath) {
                $this->error("File CSV tidak ditemukan di lokasi default.");
                $this->error("Lokasi yang dicek:");
                foreach ($defaultPaths as $path) {
                    $this->line("  - {$path}");
                }
                $this->newLine();
                $this->info("Gunakan: php artisan vendor:update-from-csv /path/to/your/file.csv");
                return Command::FAILURE;
            }
        }

        if (!file_exists($csvFilePath)) {
            $this->error("File CSV tidak ditemukan: {$csvFilePath}");
            return Command::FAILURE;
        }

        $this->info("====================================================");
        $this->info("UPDATE VENDOR INVOICE DARI CSV");
        $this->info("====================================================");
        $this->info("File CSV: {$csvFilePath}");
        $this->info("Waktu mulai: " . now()->format('Y-m-d H:i:s'));
        $this->newLine();

        try {
            $this->processCsv($csvFilePath);
            $this->showResults();
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Process the CSV file
     */
    private function processCsv($csvFilePath)
    {
        $file = fopen($csvFilePath, 'r');
        if (!$file) {
            throw new \Exception("Tidak dapat membuka file CSV");
        }

        // Read headers
        $headers = fgetcsv($file, 0, $this->delimiter);
        if (!$headers) {
            fclose($file);
            throw new \Exception("Tidak dapat membaca header CSV");
        }

        $this->info("Header CSV ditemukan:");
        foreach ($headers as $index => $header) {
            $this->line("  [{$index}] {$header}");
        }
        $this->newLine();

        // Find required columns
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

        $this->info("Mapping kolom:");
        $this->line("  - Kontainer: kolom [{$kontainerColumn}] {$headers[$kontainerColumn]}");
        $this->line("  - Invoice Vendor: kolom [{$invoiceVendorColumn}] {$headers[$invoiceVendorColumn]}");
        if ($tanggalVendorColumn !== false) {
            $this->line("  - Tanggal Vendor: kolom [{$tanggalVendorColumn}] {$headers[$tanggalVendorColumn]}");
        }
        $this->newLine();

        // Confirm before proceeding
        if (!$this->confirm('Lanjutkan update database?')) {
            fclose($file);
            $this->info('Update dibatalkan.');
            return;
        }

        // Start database transaction
        DB::beginTransaction();

        try {
            $rowNumber = 1;
            $progressBar = $this->output->createProgressBar();
            $progressBar->start();

            // Process each row
            while (($row = fgetcsv($file, 0, $this->delimiter)) !== false) {
                $rowNumber++;
                $progressBar->advance();

                // Skip empty or incomplete rows
                if (count($row) < max($kontainerColumn, $invoiceVendorColumn) + 1) {
                    continue;
                }

                $kontainer = trim($row[$kontainerColumn] ?? '');
                $invoiceVendor = trim($row[$invoiceVendorColumn] ?? '');
                $tanggalVendor = null;

                // Parse vendor date if available
                if ($tanggalVendorColumn !== false && !empty($row[$tanggalVendorColumn])) {
                    $tanggalVendor = $this->parseDate(trim($row[$tanggalVendorColumn]));
                }

                // Skip if container or invoice vendor is empty
                if (empty($kontainer) || empty($invoiceVendor)) {
                    continue;
                }

                // Update database
                $this->updateVendorInfo($kontainer, $invoiceVendor, $tanggalVendor, $rowNumber);
            }

            $progressBar->finish();
            $this->newLine(2);

            fclose($file);

            // Commit transaction
            DB::commit();
            $this->info("✅ Transaksi database berhasil di-commit");

        } catch (\Exception $e) {
            DB::rollback();
            fclose($file);
            $this->error("❌ Transaksi database di-rollback karena error");
            throw $e;
        }
    }

    /**
     * Find column index by possible names
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
     * Parse date from various formats
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
     * Update vendor information in database
     */
    private function updateVendorInfo($kontainer, $invoiceVendor, $tanggalVendor, $rowNumber)
    {
        try {
            // Find records by container number
            $records = DaftarTagihanKontainerSewa::where('nomor_kontainer', $kontainer)->get();

            if ($records->isEmpty()) {
                $this->notFoundCount++;
                $this->errors[] = "Baris {$rowNumber}: Kontainer '{$kontainer}' tidak ditemukan di database";
                return;
            }

            // Update all matching records
            $updateData = ['invoice_vendor' => $invoiceVendor];
            if ($tanggalVendor) {
                $updateData['tanggal_vendor'] = $tanggalVendor;
            }

            $updatedRecords = DaftarTagihanKontainerSewa::where('nomor_kontainer', $kontainer)
                ->update($updateData);

            $this->updatedCount += $updatedRecords;

        } catch (\Exception $e) {
            $this->errors[] = "Baris {$rowNumber}: Error updating kontainer '{$kontainer}' - " . $e->getMessage();
        }
    }

    /**
     * Show processing results
     */
    private function showResults()
    {
        $this->newLine();
        $this->info("====================================================");
        $this->info("HASIL PEMROSESAN");
        $this->info("====================================================");
        $this->line("<fg=green>✅ Total record berhasil diupdate: {$this->updatedCount}</>");
        $this->line("<fg=yellow>⚠️  Total kontainer tidak ditemukan: {$this->notFoundCount}</>");
        $this->line("<fg=red>❌ Total error: " . count($this->errors) . "</>");
        $this->info("Waktu selesai: " . now()->format('Y-m-d H:i:s'));

        if (!empty($this->errors)) {
            $this->newLine();
            $this->warn("DETAIL ERROR:");
            foreach ($this->errors as $error) {
                $this->line("  - {$error}");
            }
        }

        $this->info("====================================================");
    }
}
