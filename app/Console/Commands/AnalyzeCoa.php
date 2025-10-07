<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CoaTransaction;
use App\Models\Coa;
use App\Models\DaftarTagihanKontainerSewa;

class AnalyzeCoa extends Command
{
    protected $signature = 'coa:analyze {nomor_akun=COA007}';
    protected $description = 'Detailed analysis of COA account vs original data';

    public function handle()
    {
        $nomorAkun = $this->argument('nomor_akun');

        $coa = Coa::where('nomor_akun', $nomorAkun)->first();

        if (!$coa) {
            $this->error("COA {$nomorAkun} not found!");
            return Command::FAILURE;
        }

        $this->info("🔍 DETAILED COA ANALYSIS: {$coa->nomor_akun} - {$coa->nama_akun}");
        $this->newLine();

        // 1. COA Transactions Summary
        $coaTransactions = CoaTransaction::where('coa_id', $coa->id)->get();
        $coaTotalDebit = $coaTransactions->sum(function($t) { return (float)$t->debit; });
        $coaTotalKredit = $coaTransactions->sum(function($t) { return (float)$t->kredit; });

        $this->info("📊 COA TRANSACTIONS:");
        $this->table(
            ['Metric', 'Value'],
            [
                ['COA Transactions Count', number_format($coaTransactions->count())],
                ['COA Total Debit', 'Rp ' . number_format($coaTotalDebit, 2, ',', '.')],
                ['COA Total Kredit', 'Rp ' . number_format($coaTotalKredit, 2, ',', '.')],
                ['COA Net Balance', 'Rp ' . number_format($coaTotalDebit - $coaTotalKredit, 2, ',', '.')],
            ]
        );
        $this->newLine();

        // 2. Original Tagihan Summary
        $originalTagihans = DaftarTagihanKontainerSewa::whereNotNull('grand_total')
                                                    ->where('grand_total', '>', 0)
                                                    ->get();
        $originalTotalAmount = $originalTagihans->sum(function($t) { return (float)$t->grand_total; });

        $this->info("📋 ORIGINAL TAGIHAN DATA:");
        $this->table(
            ['Metric', 'Value'],
            [
                ['Original Tagihan Count', number_format($originalTagihans->count())],
                ['Original Total Amount', 'Rp ' . number_format($originalTotalAmount, 2, ',', '.')],
            ]
        );
        $this->newLine();

        // 3. Discrepancy Analysis
        $discrepancy = $originalTotalAmount - $coaTotalDebit;
        $this->info("⚖️ DISCREPANCY ANALYSIS:");
        $this->table(
            ['Analysis', 'Value'],
            [
                ['Original vs COA Difference', 'Rp ' . number_format($discrepancy, 2, ',', '.')],
                ['Match Status', $discrepancy == 0 ? '✅ PERFECT MATCH' : '❌ MISMATCH'],
            ]
        );

        if ($discrepancy != 0) {
            $this->newLine();
            $this->warn("🔍 INVESTIGATING MISMATCH...");

            // Find missing/extra references
            $coaReferences = $coaTransactions->pluck('nomor_referensi')->toArray();
            $expectedReferences = $originalTagihans->map(function($t) {
                return "TKS-{$t->nomor_kontainer}-P{$t->periode}";
            })->toArray();

            $missingInCoa = array_diff($expectedReferences, $coaReferences);
            $extraInCoa = array_diff($coaReferences, $expectedReferences);

            if (!empty($missingInCoa)) {
                $this->error("❌ MISSING IN COA (" . count($missingInCoa) . " items):");
                foreach (array_slice($missingInCoa, 0, 10) as $ref) {
                    $this->line("  - {$ref}");
                }
                if (count($missingInCoa) > 10) {
                    $this->line("  ... and " . (count($missingInCoa) - 10) . " more");
                }
            }

            if (!empty($extraInCoa)) {
                $this->error("❌ EXTRA IN COA (" . count($extraInCoa) . " items):");
                foreach (array_slice($extraInCoa, 0, 10) as $ref) {
                    $this->line("  - {$ref}");
                }
                if (count($extraInCoa) > 10) {
                    $this->line("  ... and " . (count($extraInCoa) - 10) . " more");
                }
            }
        }

        // 4. Date Range Analysis
        $this->newLine();
        $this->info("📅 DATE RANGE ANALYSIS:");

        $coaDateRange = [
            'min' => $coaTransactions->min('tanggal_transaksi'),
            'max' => $coaTransactions->max('tanggal_transaksi')
        ];

        $originalDateRange = [
            'min' => $originalTagihans->min('tanggal_awal'),
            'max' => $originalTagihans->max('tanggal_awal')
        ];

        $this->table(
            ['Source', 'Date Range'],
            [
                ['COA Transactions', $coaDateRange['min'] . ' to ' . $coaDateRange['max']],
                ['Original Tagihan', $originalDateRange['min'] . ' to ' . $originalDateRange['max']],
            ]
        );

        // 5. Amount Distribution
        $this->newLine();
        $this->info("💰 AMOUNT DISTRIBUTION:");

        $coaAmountGroups = $coaTransactions->groupBy(function($t) {
            $amount = (float)$t->debit;
            if ($amount == 0) return 'Zero';
            if ($amount < 500000) return 'Under 500K';
            if ($amount < 1000000) return '500K - 1M';
            if ($amount < 2000000) return '1M - 2M';
            return 'Over 2M';
        });

        foreach ($coaAmountGroups as $group => $transactions) {
            $groupTotal = $transactions->sum(function($t) { return (float)$t->debit; });
            $this->line("  {$group}: " . $transactions->count() . " transactions, Rp " . number_format($groupTotal, 0, ',', '.'));
        }

        return Command::SUCCESS;
    }
}
