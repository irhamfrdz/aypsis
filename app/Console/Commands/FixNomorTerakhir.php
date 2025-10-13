<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PembayaranUangMuka;
use App\Models\NomorTerakhir;

class FixNomorTerakhir extends Command
{
    protected $signature = 'fix:nomor-terakhir';
    protected $description = 'Fix nomor terakhir untuk pembayaran dan realisasi uang muka';

    public function handle()
    {
        // Cek pembayaran uang muka terakhir
        $lastPembayaran = PembayaranUangMuka::orderBy('id', 'desc')->first();

        if ($lastPembayaran) {
            $this->info("Last PembayaranUangMuka: " . $lastPembayaran->nomor_pembayaran);

            // Extract nomor dari format BTJ-10-25-000036
            $parts = explode('-', $lastPembayaran->nomor_pembayaran);
            $lastNumber = (int) end($parts);
            $this->info("Extracted number: " . $lastNumber);

            // Update/create nomor_terakhir untuk nomor_pembayaran (digunakan bersama pembayaran dan realisasi)
            $nomorTerakhir = NomorTerakhir::updateOrCreate(
                ['modul' => 'nomor_pembayaran'],
                [
                    'nomor_terakhir' => $lastNumber,
                    'keterangan' => 'Nomor terakhir untuk pembayaran'
                ]
            );
            $this->info("Updated nomor_pembayaran nomor_terakhir to: " . $nomorTerakhir->nomor_terakhir);

            // Juga update pembayaran_uang_muka jika ada
            $pembayaranNomor = NomorTerakhir::updateOrCreate(
                ['modul' => 'pembayaran_uang_muka'],
                [
                    'nomor_terakhir' => $lastNumber,
                    'keterangan' => 'Nomor terakhir untuk pembayaran uang muka'
                ]
            );
            $this->info("Updated pembayaran_uang_muka nomor_terakhir to: " . $pembayaranNomor->nomor_terakhir);
        } else {
            $this->warn("No PembayaranUangMuka found");
        }

        // Hapus entry realisasi_uang_muka yang tidak digunakan lagi
        $deletedRows = NomorTerakhir::where('modul', 'realisasi_uang_muka')->delete();
        if ($deletedRows > 0) {
            $this->info("Deleted old realisasi_uang_muka entries: " . $deletedRows);
        }

        // Cek nomor_pembayaran yang akan digunakan
        $nomorPembayaran = NomorTerakhir::where('modul', 'nomor_pembayaran')->first();
        if ($nomorPembayaran) {
            $this->info("Current nomor_pembayaran nomor_terakhir: " . $nomorPembayaran->nomor_terakhir);
            $this->info("Next nomor will be: " . str_pad($nomorPembayaran->nomor_terakhir + 1, 6, '0', STR_PAD_LEFT));
        } else {
            $this->warn("No nomor_pembayaran entry found");
        }

        $this->info("Done! Realisasi uang muka sekarang akan mengikuti nomor_pembayaran yang sama dengan pembayaran uang muka.");
        return Command::SUCCESS;
    }
}
