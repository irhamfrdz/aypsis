<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DaftarTagihanKontainerSewa;
use Illuminate\Support\Facades\DB;

class CleanTagihanKontainerSewa extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tagihan:clean
                            {--force : Force deletion without confirmation}
                            {--backup : Create backup before cleaning}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean all data from daftar_tagihan_kontainer_sewa table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== CLEAN DAFTAR TAGIHAN KONTAINER SEWA ===');

        // Check current data count
        $currentCount = DaftarTagihanKontainerSewa::count();
        $this->info("Current records: {$currentCount}");

        if ($currentCount == 0) {
            $this->info('Database is already empty. Nothing to clean.');
            return 0;
        }

        // Create backup if requested
        if ($this->option('backup')) {
            $this->createBackup();
        }

        // Confirmation
        if (!$this->option('force')) {
            if (!$this->confirm('Are you sure you want to delete all tagihan kontainer sewa data? This action cannot be undone!')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $this->info('Starting database cleanup...');

        try {
            DB::beginTransaction();

            // Disable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Delete all records
            $deletedCount = DB::table('daftar_tagihan_kontainer_sewa')->delete();

            // Reset auto increment
            DB::statement('ALTER TABLE daftar_tagihan_kontainer_sewa AUTO_INCREMENT = 1;');

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            DB::commit();

            $this->info("✅ Successfully deleted {$deletedCount} records");
            $this->info("🔄 Auto increment ID reset to 1");

            // Verify cleanup
            $finalCount = DaftarTagihanKontainerSewa::count();
            $this->info("✅ Verification: Current records: {$finalCount}");

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error during cleanup: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Create a backup of the data before cleaning
     */
    private function createBackup()
    {
        $this->info('Creating backup...');

        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "tagihan_backup_{$timestamp}.sql";
        $backupPath = storage_path("backups/{$filename}");

        // Create backups directory if it doesn't exist
        if (!is_dir(storage_path('backups'))) {
            mkdir(storage_path('backups'), 0755, true);
        }

        // Create SQL dump
        $dbName = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');

        $command = sprintf(
            'mysqldump -h%s -u%s -p%s %s daftar_tagihan_kontainer_sewa > %s',
            $host,
            $username,
            $password,
            $dbName,
            $backupPath
        );

        exec($command, $output, $returnCode);

        if ($returnCode === 0) {
            $this->info("✅ Backup created: {$backupPath}");
        } else {
            $this->warn("⚠️ Backup failed, but continuing with cleanup...");
        }
    }
}
