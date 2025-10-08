<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StockKontainer;
use App\Models\Kontainer;
use Illuminate\Support\Facades\DB;

class SyncKontainerDuplicates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kontainer:sync-duplicates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync container duplicates between stock_kontainers and kontainers tables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting container duplicate synchronization...');

        // Cari duplikasi antara stock_kontainers dan kontainers
        $duplicates = DB::select("
            SELECT sk.nomor_seri_gabungan,
                   COUNT(CASE WHEN sk.status = 'active' THEN 1 END) as active_stock_count,
                   COUNT(CASE WHEN k.status = 'active' THEN 1 END) as active_kontainer_count
            FROM stock_kontainers sk
            INNER JOIN kontainers k ON sk.nomor_seri_gabungan = k.nomor_seri_gabungan
            WHERE sk.nomor_seri_gabungan IS NOT NULL
            AND k.nomor_seri_gabungan IS NOT NULL
            GROUP BY sk.nomor_seri_gabungan
        ");

        if (empty($duplicates)) {
            $this->info('No duplicates found between stock_kontainers and kontainers.');
        } else {
            $this->info('Found ' . count($duplicates) . ' container numbers with duplicates:');

            foreach ($duplicates as $duplicate) {
                $this->line("Processing: {$duplicate->nomor_seri_gabungan}");

                // Set stock_kontainer ke inactive jika ada kontainer aktif
                if ($duplicate->active_kontainer_count > 0) {
                    StockKontainer::where('nomor_seri_gabungan', $duplicate->nomor_seri_gabungan)
                        ->where('status', 'active')
                        ->update(['status' => 'inactive']);

                    $this->info("  → Stock kontainer set to inactive (kontainer is active)");
                }
            }
        }

        // Cari duplikasi dalam stock_kontainers sendiri
        $stockDuplicates = DB::select("
            SELECT nomor_seri_gabungan, COUNT(*) as count
            FROM stock_kontainers
            WHERE nomor_seri_gabungan IS NOT NULL
            AND status = 'active'
            GROUP BY nomor_seri_gabungan
            HAVING COUNT(*) > 1
        ");

        if (!empty($stockDuplicates)) {
            $this->info('Found ' . count($stockDuplicates) . ' duplicate numbers in stock_kontainers:');

            foreach ($stockDuplicates as $duplicate) {
                $this->line("Processing stock duplicates: {$duplicate->nomor_seri_gabungan}");

                // Ambil yang terbaru, set yang lama ke inactive
                $stocks = StockKontainer::where('nomor_seri_gabungan', $duplicate->nomor_seri_gabungan)
                    ->where('status', 'active')
                    ->orderBy('created_at', 'desc')
                    ->get();

                // Skip yang pertama (terbaru), set yang lain ke inactive
                $stocks->skip(1)->each(function ($stock) {
                    $stock->update(['status' => 'inactive']);
                });

                $this->info("  → " . ($stocks->count() - 1) . " old stock entries set to inactive");
            }
        }

        // Cari duplikasi dalam kontainers sendiri berdasarkan nomor_seri_kontainer + akhiran_kontainer
        $kontainerSerialDuplicates = DB::select("
            SELECT nomor_seri_kontainer, akhiran_kontainer, COUNT(*) as count
            FROM kontainers
            WHERE nomor_seri_kontainer IS NOT NULL
            AND akhiran_kontainer IS NOT NULL
            AND status = 'active'
            GROUP BY nomor_seri_kontainer, akhiran_kontainer
            HAVING COUNT(*) > 1
        ");

        if (!empty($kontainerSerialDuplicates)) {
            $this->info('Found ' . count($kontainerSerialDuplicates) . ' duplicate serial+suffix combinations in kontainers:');

            foreach ($kontainerSerialDuplicates as $duplicate) {
                $this->line("Processing kontainer serial duplicates: {$duplicate->nomor_seri_kontainer}-{$duplicate->akhiran_kontainer}");

                // Ambil yang terbaru, set yang lama ke inactive
                $kontainers = Kontainer::where('nomor_seri_kontainer', $duplicate->nomor_seri_kontainer)
                    ->where('akhiran_kontainer', $duplicate->akhiran_kontainer)
                    ->where('status', 'active')
                    ->orderBy('created_at', 'desc')
                    ->get();

                // Skip yang pertama (terbaru), set yang lain ke inactive
                $kontainers->skip(1)->each(function ($kontainer) {
                    $kontainer->update(['status' => 'inactive']);
                });

                $this->info("  → " . ($kontainers->count() - 1) . " old kontainer entries with same serial+suffix set to inactive");
            }
        }

        // Cari duplikasi dalam kontainers sendiri
        $kontainerDuplicates = DB::select("
            SELECT nomor_seri_gabungan, COUNT(*) as count
            FROM kontainers
            WHERE nomor_seri_gabungan IS NOT NULL
            AND status = 'active'
            GROUP BY nomor_seri_gabungan
            HAVING COUNT(*) > 1
        ");

        if (!empty($kontainerDuplicates)) {
            $this->info('Found ' . count($kontainerDuplicates) . ' duplicate numbers in kontainers:');

            foreach ($kontainerDuplicates as $duplicate) {
                $this->line("Processing kontainer duplicates: {$duplicate->nomor_seri_gabungan}");

                // Ambil yang terbaru, set yang lama ke inactive
                $kontainers = Kontainer::where('nomor_seri_gabungan', $duplicate->nomor_seri_gabungan)
                    ->where('status', 'active')
                    ->orderBy('created_at', 'desc')
                    ->get();

                // Skip yang pertama (terbaru), set yang lain ke inactive
                $kontainers->skip(1)->each(function ($kontainer) {
                    $kontainer->update(['status' => 'inactive']);
                });

                $this->info("  → " . ($kontainers->count() - 1) . " old kontainer entries set to inactive");
            }
        }

        $this->info('Container duplicate synchronization completed!');
        return 0;
    }
}
