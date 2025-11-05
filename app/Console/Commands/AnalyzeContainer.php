<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AnalyzeContainer extends Command
{
    protected $signature = 'analyze:container {container=RXTU4540180}';
    protected $description = 'Analyze specific container periode details';

    public function handle()
    {
        $containerNo = $this->argument('container');
        
        $this->info("=== ANALYZING CONTAINER: $containerNo ===");
        
        $containers = DB::table('daftar_tagihan_kontainer_sewa')
            ->where('nomor_kontainer', $containerNo)
            ->select('nomor_kontainer', 'tanggal_awal', 'tanggal_akhir', 'periode', 'vendor')
            ->orderBy('periode')
            ->get();

        if($containers->count() == 0) {
            $this->error("Container $containerNo not found!");
            return;
        }

        $this->info("Found {$containers->count()} periods for container $containerNo");
        $this->info("");
        
        foreach($containers as $record) {
            $this->line("Periode: {$record->periode} | Start: {$record->tanggal_awal} | End: {$record->tanggal_akhir} | Vendor: {$record->vendor}");
        }
        
        $firstRecord = $containers->first();
        $lastRecord = $containers->last();
        
        $this->info("");
        $this->info("=== ANALYSIS ===");
        $this->info("First periode: {$firstRecord->periode} - Start: {$firstRecord->tanggal_awal}");
        $this->info("Last periode: {$lastRecord->periode} - End: {$lastRecord->tanggal_akhir}");
        $this->info("Max periode found: {$lastRecord->periode}");
        
        // Check if container should continue
        if($lastRecord->tanggal_akhir) {
            $endDate = \Carbon\Carbon::parse($lastRecord->tanggal_akhir);
            $now = \Carbon\Carbon::now();
            $this->info("End date: " . $endDate->format('Y-m-d'));
            $this->info("Current date: " . $now->format('Y-m-d'));
            
            if($now->greaterThan($endDate)) {
                $this->warn("⚠️  Current date is AFTER end date - periode should have stopped");
            } else {
                $this->info("✅ Current date is before end date - container still active");
            }
        } else {
            $this->info("No end date - container should continue indefinitely");
        }
    }
}