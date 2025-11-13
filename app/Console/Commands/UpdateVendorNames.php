<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateVendorNames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vendor:update-names 
                            {--dry-run : Run without making changes}
                            {--table= : Specific table to update}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update vendor names from full company names to short codes (DPE, ZONA)';

    /**
     * Vendor mapping configuration
     *
     * @var array
     */
    protected $vendorMapping = [
        'PT. DEPO PETIKEMAS EXPRESSINDO' => 'DPE',
        'PT. ZONA LINTAS SAMUDERA' => 'ZONA',
    ];

    /**
     * Tables to update
     *
     * @var array
     */
    protected $tables = [
        'kontainers',
        'tagihan_kontainer_sewa',
        'pranota_tagihan_kontainers',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $specificTable = $this->option('table');
        $totalUpdated = 0;

        $this->info('=================================================');
        $this->info('  UPDATE VENDOR NAMES');
        $this->info('=================================================');
        $this->newLine();

        if ($isDryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        // Filter tables if specific table is provided
        $tablesToProcess = $specificTable 
            ? [$specificTable] 
            : $this->tables;

        try {
            if (!$isDryRun) {
                DB::beginTransaction();
            }

            foreach ($tablesToProcess as $tableName) {
                if (!Schema::hasTable($tableName)) {
                    $this->warn("⚠️  Table '{$tableName}' does not exist. Skipping...");
                    $this->newLine();
                    continue;
                }

                $this->info("Processing table: {$tableName}");
                $this->line('-----------------------------------');

                $tableUpdated = 0;

                foreach ($this->vendorMapping as $oldName => $newName) {
                    // Count records to be updated
                    $count = DB::table($tableName)
                        ->where('vendor', $oldName)
                        ->count();

                    if ($count > 0) {
                        if (!$isDryRun) {
                            // Perform the update
                            DB::table($tableName)
                                ->where('vendor', $oldName)
                                ->update(['vendor' => $newName]);
                        }

                        $this->line("  ✓ '{$oldName}' → '{$newName}': <fg=green>{$count} records</>");
                        $tableUpdated += $count;
                        $totalUpdated += $count;
                    } else {
                        $this->line("  ○ No records found for '{$oldName}'");
                    }
                }

                if ($tableUpdated > 0) {
                    $this->info("  Subtotal: {$tableUpdated} records");
                }
                $this->newLine();
            }

            if (!$isDryRun) {
                DB::commit();
            }

            $this->newLine();
            $this->info('=================================================');
            
            if ($isDryRun) {
                $this->info("✓ DRY RUN COMPLETE! Would update: {$totalUpdated} records");
            } else {
                $this->info("✓ SUCCESS! Total records updated: {$totalUpdated}");
            }
            
            $this->info('=================================================');
            $this->newLine();

            // Display final vendor summary
            if (!$isDryRun && Schema::hasTable('kontainers')) {
                $this->displayVendorSummary();
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            if (!$isDryRun) {
                DB::rollBack();
            }

            $this->newLine();
            $this->error('=================================================');
            $this->error('✗ ERROR: ' . $e->getMessage());
            $this->error('=================================================');
            $this->newLine();

            if (!$isDryRun) {
                $this->warn('Transaction rolled back. No changes were made.');
            }

            return Command::FAILURE;
        }
    }

    /**
     * Display vendor summary from kontainers table
     */
    protected function displayVendorSummary()
    {
        $this->info('Final Vendor Summary in kontainers table:');
        $this->line('-----------------------------------');

        $vendors = DB::table('kontainers')
            ->select('vendor', DB::raw('count(*) as total'))
            ->whereNotNull('vendor')
            ->groupBy('vendor')
            ->orderBy('vendor')
            ->get();

        $headers = ['Vendor', 'Total Records'];
        $rows = [];

        foreach ($vendors as $vendor) {
            $rows[] = [$vendor->vendor, $vendor->total];
        }

        $this->table($headers, $rows);
        $this->newLine();
    }
}
