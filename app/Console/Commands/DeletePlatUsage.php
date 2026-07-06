<?php

namespace App\Console\Commands;

use App\Models\StockAmprahan;
use App\Models\StockAmprahanUsage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeletePlatUsage extends Command
{
    protected $signature = 'stock:delete-plat-usage {--dry-run : Hanya tampilkan pemakaian yang akan dihapus tanpa melakukan perubahan}';

    protected $description = 'Hapus catatan pemakaian stock untuk Plat 12mm dan Plat 10mm serta mengembalikan jumlah stock ke induk';

    public function handle()
    {
        $dryRun = $this->option('dry-run');

        // Cari data stock induk untuk plat 12mm dan plat 10mm
        $stockItems = StockAmprahan::where(function($query) {
            $query->where('nama_barang', 'like', '%plat 12mm%')
                  ->orWhere('nama_barang', 'like', '%plat 10mm%')
                  ->orWhere('nama_barang', 'like', '%plat 12 mm%')
                  ->orWhere('nama_barang', 'like', '%plat 10 mm%');
        })->get();

        if ($stockItems->isEmpty()) {
            $this->warn('Tidak ditemukan data stock induk untuk Plat 12mm atau Plat 10mm.');
            return Command::FAILURE;
        }

        $this->info("Ditemukan " . $stockItems->count() . " item stock induk:");
        foreach ($stockItems as $item) {
            $this->line("- ID: {$item->id} | {$item->nama_barang} | Sisa Stock Saat Ini: {$item->jumlah} {$item->satuan}");
        }

        $stockIds = $stockItems->pluck('id');
        $usages = StockAmprahanUsage::whereIn('stock_amprahan_id', $stockIds)->get();

        if ($usages->isEmpty()) {
            $this->info("\nTidak ada catatan pemakaian (usages) yang ditemukan untuk item-item tersebut.");
            return Command::SUCCESS;
        }

        $this->info("\nDitemukan " . $usages->count() . " catatan pemakaian:");
        foreach ($usages as $usage) {
            $namaBarang = $usage->stockAmprahan->nama_barang ?? 'Tidak Diketahui';
            $penerima = $usage->penerima->nama ?? '-';
            
            $tanggal = $usage->tanggal_pengambilan;
            if ($tanggal instanceof \Carbon\Carbon) {
                $tanggalStr = $tanggal->format('Y-m-d');
            } elseif (is_string($tanggal)) {
                $tanggalStr = date('Y-m-d', strtotime($tanggal));
            } else {
                $tanggalStr = '-';
            }

            $this->line("  * Usage ID: {$usage->id} | Barang: {$namaBarang} | Jumlah: {$usage->jumlah} | Tanggal: {$tanggalStr} | Penerima: {$penerima}");
        }

        if ($dryRun) {
            $this->info("\n[DRY RUN] Tidak ada perubahan yang dilakukan pada database.");
            return Command::SUCCESS;
        }

        if (!$this->confirm('Apakah Anda yakin ingin menghapus catatan pemakaian di atas dan mengembalikan stock-nya?')) {
            $this->info('Proses dibatalkan.');
            return Command::SUCCESS;
        }

        $this->info("\nMemproses penghapusan pemakaian...");

        DB::transaction(function () use ($usages) {
            foreach ($usages as $usage) {
                $stock = $usage->stockAmprahan;
                if ($stock) {
                    $stock->increment('jumlah', $usage->jumlah);
                    $this->info("Stock ID {$stock->id} ({$stock->nama_barang}) bertambah {$usage->jumlah}.");
                }
                $usage->delete();
                $this->info("Usage ID {$usage->id} telah dihapus.");
            }
        });

        $this->info("\nSelesai! Seluruh pemakaian plat 12mm dan plat 10mm berhasil dihapus dan stock dikembalikan.");
        return Command::SUCCESS;
    }
}
