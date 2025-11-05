<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FindContainer extends Command
{
    protected $signature = 'find:container';
    protected $description = 'Find container RXTU548';

    public function handle()
    {
        $this->info('=== SEARCHING FOR CONTAINER VARIATIONS ===');
        
        $containers = DB::table('daftar_tagihan_kontainer_sewa')
            ->where('nomor_kontainer', 'like', '%RXTU548%')
            ->select('nomor_kontainer', 'tanggal_awal', 'tanggal_akhir', 'periode')
            ->orderBy('nomor_kontainer')
            ->orderBy('periode')
            ->get();

        $this->info("Found containers with RXTU548: " . $containers->count());
        
        if($containers->count() > 0) {
            $grouped = [];
            foreach($containers as $c) {
                $grouped[$c->nomor_kontainer][] = $c;
            }

            foreach($grouped as $containerNo => $records) {
                $this->info("Container: $containerNo");
                foreach($records as $record) {
                    $this->line("  Periode: {$record->periode}, Start: {$record->tanggal_awal}, End: {$record->tanggal_akhir}");
                }
            }
        }
        
        $this->info('=== CONTAINERS WITH EXACTLY 7 PERIODS ===');
        $containers7 = DB::table('daftar_tagihan_kontainer_sewa')
            ->select('nomor_kontainer', DB::raw('MAX(periode) as max_periode'))
            ->groupBy('nomor_kontainer')
            ->having('max_periode', '=', 7)
            ->limit(10)
            ->get();

        foreach($containers7 as $c) {
            $this->info("Container: {$c->nomor_kontainer} - Max Periode: {$c->max_periode}");
        }
    }
}