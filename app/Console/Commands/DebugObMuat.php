<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MasterKapal;
use App\Models\PergerakanKapal;

class DebugObMuat extends Command
{
    protected $signature = 'debug:ob-muat';
    protected $description = 'Debug OB Muat data';

    public function handle()
    {
        $this->info('=== DEBUG OB MUAT DATA ===');
        $this->newLine();

        // Master Kapal
        $this->info('1. MASTER KAPAL:');
        $masterKapals = MasterKapal::where('status', 'aktif')->orderBy('nama_kapal')->get();
        $this->info("Total: " . $masterKapals->count());
        $masterKapals->take(10)->each(function($kapal) {
            $this->line("- {$kapal->nama_kapal} (Status: {$kapal->status})");
        });

        $this->newLine();
        $this->info('2. PERGERAKAN KAPAL:');
        $pergerakanKapals = PergerakanKapal::whereNotNull('voyage')
                                          ->where('voyage', '!=', '')
                                          ->orderBy('nama_kapal')
                                          ->orderBy('voyage')
                                          ->get();
        $this->info("Total: " . $pergerakanKapals->count());
        $pergerakanKapals->take(10)->each(function($pergerakan) {
            $this->line("- {$pergerakan->nama_kapal} | Voyage: {$pergerakan->voyage} | Status: {$pergerakan->status}");
        });

        $this->newLine();
        $this->info('3. GROUPING BY KAPAL:');
        $groupedVoyages = $pergerakanKapals->groupBy('nama_kapal');
        foreach($groupedVoyages->take(5) as $namaKapal => $voyages) {
            $this->line("Kapal: $namaKapal");
            foreach($voyages as $voyage) {
                $this->line("  - Voyage: {$voyage->voyage} ({$voyage->status})");
            }
            $this->newLine();
        }

        $this->newLine();
        $this->info('4. NAME MATCHING:');
        $masterNames = $masterKapals->pluck('nama_kapal')->toArray();
        $pergerakanNames = $pergerakanKapals->pluck('nama_kapal')->unique()->toArray();
        $commonNames = array_intersect($masterNames, $pergerakanNames);

        $this->info("Common names: " . count($commonNames));
        foreach($commonNames as $name) {
            $this->line("- $name");
        }

        $this->info("Master only: " . count(array_diff($masterNames, $pergerakanNames)));
        $this->info("Pergerakan only: " . count(array_diff($pergerakanNames, $masterNames)));

        $this->newLine();
        $this->info('=== END DEBUG ===');
    }
}