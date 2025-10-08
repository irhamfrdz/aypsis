<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StockKontainer;
use App\Models\Kontainer;
use Illuminate\Support\Facades\DB;

class ValidateDuplicateKontainers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kontainer:validate-duplicates {--fix : Automatically fix duplicates by setting stock_kontainer status to inactive}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validate and optionally fix duplicate kontainer numbers between stock_kontainers and kontainers tables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for duplicate kontainer numbers...');

        // Find duplicates
        $duplicates = DB::select("
            SELECT sk.nomor_seri_gabungan, sk.status as stock_status, k.status as kontainer_status
            FROM stock_kontainers sk
            INNER JOIN kontainers k ON sk.nomor_seri_gabungan = k.nomor_seri_gabungan
            WHERE sk.nomor_seri_gabungan IS NOT NULL
            AND k.nomor_seri_gabungan IS NOT NULL
        ");

        if (empty($duplicates)) {
            $this->info('✅ No duplicate kontainer numbers found.');
            return 0;
        }

        $this->warn('🔍 Found ' . count($duplicates) . ' duplicate kontainer numbers:');

        $headers = ['Nomor Kontainer', 'Stock Status', 'Kontainer Status', 'Action Needed'];
        $rows = [];

        foreach ($duplicates as $duplicate) {
            $actionNeeded = ($duplicate->stock_status !== 'inactive') ? 'Set to inactive' : 'Already inactive';
            $rows[] = [
                $duplicate->nomor_seri_gabungan,
                $duplicate->stock_status,
                $duplicate->kontainer_status,
                $actionNeeded
            ];
        }

        $this->table($headers, $rows);

        if ($this->option('fix')) {
            $this->info('🔧 Fixing duplicates...');

            $fixed = 0;
            foreach ($duplicates as $duplicate) {
                if ($duplicate->stock_status !== 'inactive') {
                    DB::table('stock_kontainers')
                        ->where('nomor_seri_gabungan', $duplicate->nomor_seri_gabungan)
                        ->update(['status' => 'inactive']);

                    $this->line("  ✓ {$duplicate->nomor_seri_gabungan} - Stock kontainer set to inactive");
                    $fixed++;
                }
            }

            $this->info("✅ Fixed {$fixed} duplicate records.");
        } else {
            $this->info('💡 Use --fix option to automatically set duplicate stock_kontainers to inactive status.');
        }

        return 0;
    }
}
