<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckContainerBehavior extends Command
{
    protected $signature = 'check:containers';
    protected $description = 'Check container behavior patterns';

    public function handle()
    {
        $this->info("=== CONTAINER BEHAVIOR ANALYSIS ===");
        
        // Check containers without end dates (ongoing)
        $ongoingContainers = DB::table('daftar_tagihan_kontainer_sewa')
            ->whereNull('tanggal_akhir')
            ->select('nomor_kontainer', DB::raw('MAX(periode) as max_periode'))
            ->groupBy('nomor_kontainer')
            ->limit(5)
            ->get();
        
        $this->info("Ongoing containers (no end date):");
        foreach($ongoingContainers as $c) {
            $this->line("  {$c->nomor_kontainer} - Max Periode: {$c->max_periode}");
        }
        
        $this->info("");
        
        // Check containers with end dates that have passed
        $expiredContainers = DB::table('daftar_tagihan_kontainer_sewa')
            ->whereNotNull('tanggal_akhir')
            ->where('tanggal_akhir', '<', now())
            ->select('nomor_kontainer', 'tanggal_akhir', DB::raw('MAX(periode) as max_periode'))
            ->groupBy('nomor_kontainer', 'tanggal_akhir')
            ->limit(5)
            ->get();
        
        $this->info("Containers with expired end dates:");
        foreach($expiredContainers as $c) {
            $this->line("  {$c->nomor_kontainer} - End: {$c->tanggal_akhir} - Max Periode: {$c->max_periode}");
        }
        
        $this->info("");
        
        // Check containers with future end dates
        $futureContainers = DB::table('daftar_tagihan_kontainer_sewa')
            ->whereNotNull('tanggal_akhir')
            ->where('tanggal_akhir', '>', now())
            ->select('nomor_kontainer', 'tanggal_akhir', DB::raw('MAX(periode) as max_periode'))
            ->groupBy('nomor_kontainer', 'tanggal_akhir')
            ->limit(5)
            ->get();
        
        $this->info("Containers with future end dates:");
        foreach($futureContainers as $c) {
            $this->line("  {$c->nomor_kontainer} - End: {$c->tanggal_akhir} - Max Periode: {$c->max_periode}");
        }
    }
}