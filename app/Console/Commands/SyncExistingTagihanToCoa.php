<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DaftarTagihanKontainerSewa;
use App\Models\Coa;
use App\Models\CoaTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SyncExistingTagihanToCoa extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tagihan:sync-to-coa
                            {--dry-run : Show what would be synced without actually creating transactions}
                            {--force : Force sync even if transactions already exist}';

    /**
     * The console command description.
     */
    protected $description = 'Sync existing tagihan kontainer sewa to COA007 transactions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Syncing existing tagihan to COA007...');

        $isDryRun = $this->option('dry-run');
        $isForce = $this->option('force');

        if ($isDryRun) {
            $this->warn('🧪 DRY RUN MODE - No actual transactions will be created');
        }

        // Find COA007 account
        $coa = Coa::where('nomor_akun', 'COA007')->first();

        if (!$coa) {
            $this->error('❌ COA007 account not found! Please create COA007 first.');
            return Command::FAILURE;
        }

        $this->info("✅ Found COA007: {$coa->nama_akun}");

        // Get all tagihan that don't have COA transactions yet
        $query = DaftarTagihanKontainerSewa::query();

        if (!$isForce) {
            // Only get tagihan that don't have COA transactions yet
            $existingRefs = CoaTransaction::where('jenis_transaksi', 'tagihan_kontainer_sewa')
                                       ->pluck('nomor_referensi')
                                       ->toArray();

            $query->whereNotIn('id', function($subQuery) use ($existingRefs) {
                $subQuery->select('id')
                        ->from('daftar_tagihan_kontainer_sewa')
                        ->whereIn(
                            DB::raw("CONCAT('TKS-', nomor_kontainer, '-P', periode)"),
                            $existingRefs
                        );
            });
        }

        $tagihans = $query->whereNotNull('grand_total')
                         ->where('grand_total', '>', 0)
                         ->orderBy('tanggal_awal')
                         ->orderBy('nomor_kontainer')
                         ->orderBy('periode')
                         ->get();

        if ($tagihans->isEmpty()) {
            $this->info('ℹ️  No tagihan found to sync (all might be already synced)');
            return Command::SUCCESS;
        }

        $this->info("📊 Found {$tagihans->count()} tagihan to sync");

        // Show summary
        $totalAmount = $tagihans->sum('grand_total');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Tagihan', number_format($tagihans->count())],
                ['Total Amount', 'Rp ' . number_format($totalAmount, 2, ',', '.')],
                ['Date Range', $tagihans->min('tanggal_awal') . ' to ' . $tagihans->max('tanggal_awal')],
                ['Containers', $tagihans->pluck('nomor_kontainer')->unique()->count() . ' unique']
            ]
        );

        if ($isDryRun) {
            $this->info('🧪 DRY RUN - Showing first 10 tagihan that would be synced:');
            $this->table(
                ['Container', 'Periode', 'Amount', 'Reference'],
                $tagihans->take(10)->map(function($tagihan) {
                    return [
                        $tagihan->nomor_kontainer,
                        $tagihan->periode,
                        'Rp ' . number_format((float)$tagihan->grand_total, 2, ',', '.'),
                        "TKS-{$tagihan->nomor_kontainer}-P{$tagihan->periode}"
                    ];
                })->toArray()
            );
            return Command::SUCCESS;
        }

        // Confirm before proceeding
        if (!$this->confirm("🚀 Proceed to create {$tagihans->count()} COA transactions?")) {
            $this->info('❌ Operation cancelled');
            return Command::SUCCESS;
        }

        $successCount = 0;
        $errorCount = 0;
        $progressBar = $this->output->createProgressBar($tagihans->count());
        $progressBar->start();

        foreach ($tagihans as $tagihan) {
            try {
                $this->createCoaTransaction($coa, $tagihan);
                $successCount++;
            } catch (\Exception $e) {
                $errorCount++;
                $this->newLine();
                $this->error("❌ Failed for {$tagihan->nomor_kontainer} P{$tagihan->periode}: " . $e->getMessage());
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Update all saldos at once for better performance
        if ($successCount > 0) {
            $this->info('🔄 Updating COA saldos...');
            $this->updateAllCoaSaldos($coa->id);
        }

        // Final summary
        $this->info("✅ Sync completed!");
        $this->table(
            ['Result', 'Count'],
            [
                ['✅ Success', $successCount],
                ['❌ Errors', $errorCount],
                ['📊 Total', $tagihans->count()]
            ]
        );

        if ($successCount > 0) {
            $this->info("💰 COA007 balance should now reflect Rp " . number_format($totalAmount, 2, ',', '.'));
            $this->info("🔍 Check COA007 transactions in your accounting system");
        }

        return Command::SUCCESS;
    }

    /**
     * Create COA transaction for tagihan
     */
    private function createCoaTransaction($coa, $tagihan)
    {
        $debitAmount = (float) $tagihan->grand_total;
        $referenceNumber = "TKS-{$tagihan->nomor_kontainer}-P{$tagihan->periode}";

        // Check if transaction already exists
        $existing = CoaTransaction::where('nomor_referensi', $referenceNumber)->first();
        if ($existing && !$this->option('force')) {
            throw new \Exception("Transaction already exists: {$referenceNumber}");
        }

        $transaksi = CoaTransaction::create([
            'coa_id' => $coa->id,
            'tanggal_transaksi' => $tagihan->tanggal_awal ?? Carbon::now()->format('Y-m-d'),
            'keterangan' => "Tagihan periode {$tagihan->periode} - Kontainer {$tagihan->nomor_kontainer} ({$tagihan->vendor})",
            'debit' => $debitAmount,
            'kredit' => 0,
            'saldo' => 0, // Will be calculated later
            'jenis_transaksi' => 'tagihan_kontainer_sewa',
            'nomor_referensi' => $referenceNumber,
            'created_by' => 1, // Default admin user ID
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        return $transaksi;
    }

    /**
     * Update all COA saldos efficiently
     */
    private function updateAllCoaSaldos($coaId)
    {
        $transactions = CoaTransaction::where('coa_id', $coaId)
                                    ->orderBy('tanggal_transaksi', 'asc')
                                    ->orderBy('id', 'asc')
                                    ->get();

        $runningSaldo = 0;
        foreach ($transactions as $transaction) {
            $runningSaldo = $runningSaldo + $transaction->debit - $transaction->kredit;
            $transaction->update(['saldo' => $runningSaldo]);
        }
    }
}
