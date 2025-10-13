<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DaftarTagihanKontainerSewa;
use Illuminate\Support\Facades\DB;

class FindDuplicateTagihan extends Command
{
    protected $signature = 'tagihan:find-duplicates {--fix : Fix duplicates by removing extras}';
    protected $description = 'Find and optionally fix duplicate tagihan records';

    public function handle()
    {
        $this->info('=== MENCARI DATA DUPLIKAT ===');
        $this->newLine();

        // Query untuk mencari duplikat
        $duplicates = DB::select("
            SELECT nomor_kontainer, periode, COUNT(*) as count 
            FROM daftar_tagihan_kontainer_sewa 
            GROUP BY nomor_kontainer, periode 
            HAVING COUNT(*) > 1 
            ORDER BY count DESC, nomor_kontainer, periode
        ");

        $this->info('Duplikat ditemukan: ' . count($duplicates) . ' pasang');
        $this->newLine();

        $totalDuplicateRecords = 0;

        foreach($duplicates as $dup) {
            $this->warn("Kontainer: {$dup->nomor_kontainer} | Periode: {$dup->periode} | Jumlah: {$dup->count} records");
            
            // Ambil detail dari duplikat ini
            $details = DaftarTagihanKontainerSewa::where('nomor_kontainer', $dup->nomor_kontainer)
                ->where('periode', $dup->periode)
                ->orderBy('created_at', 'ASC')
                ->get();
            
            foreach($details as $i => $detail) {
                $status = $i == 0 ? '[PERTAMA - KEEP]' : '[DUPLIKAT - DELETE?]';
                $this->line("  {$status} ID: {$detail->id} | Masa: {$detail->masa} | DPP: " . number_format($detail->dpp) . " | Created: {$detail->created_at}");
                
                if ($i > 0) {
                    $totalDuplicateRecords++;
                }
            }
            $this->newLine();
        }

        $this->info("Total record duplikat yang bisa dihapus: {$totalDuplicateRecords}");

        if ($this->option('fix') && $totalDuplicateRecords > 0) {
            if ($this->confirm('Hapus semua record duplikat? (hanya menyisakan yang pertama kali dibuat)')) {
                $deletedCount = 0;
                
                foreach($duplicates as $dup) {
                    $records = DaftarTagihanKontainerSewa::where('nomor_kontainer', $dup->nomor_kontainer)
                        ->where('periode', $dup->periode)
                        ->orderBy('created_at', 'ASC')
                        ->get();
                    
                    // Hapus semua kecuali yang pertama
                    for($i = 1; $i < $records->count(); $i++) {
                        $records[$i]->delete();
                        $deletedCount++;
                        $this->info("Deleted: {$records[$i]->nomor_kontainer} periode {$records[$i]->periode} (ID: {$records[$i]->id})");
                    }
                }
                
                $this->success("Berhasil menghapus {$deletedCount} record duplikat!");
            }
        }

        $this->info('=== SELESAI ===');
        return 0;
    }
}