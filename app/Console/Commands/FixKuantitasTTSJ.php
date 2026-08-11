<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TandaTerimaTanpaSuratJalan;
use App\Models\Prospek;
use App\Models\NaikKapal;
use App\Models\Manifest;
use Illuminate\Support\Facades\DB;

class FixKuantitasTTSJ extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:kuantitas-ttsj';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix kuantitas in TandaTerimaTanpaSuratJalan, Prospek, NaikKapal, and Manifest by summing items jumlah';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to fix kuantitas for Tanda Terima Tanpa Surat Jalan...');

        $ttssjs = TandaTerimaTanpaSuratJalan::with('dimensiItems')->get();
        $fixedCount = 0;

        foreach ($ttssjs as $ttsj) {
            $realTotalKuantitas = $ttsj->dimensiItems->sum('jumlah');

            // Skip if no items or total is 0
            if ($realTotalKuantitas == 0) {
                continue;
            }

            // If current data is wrong
            if ($ttsj->jumlah_barang != $realTotalKuantitas) {
                $oldKuantitas = $ttsj->jumlah_barang;
                
                DB::transaction(function () use ($ttsj, $realTotalKuantitas, $oldKuantitas) {
                    // Update TTSJ
                    $ttsj->update(['jumlah_barang' => $realTotalKuantitas]);
                    
                    // Update Prospeks linked to this TTSJ
                    $noTandaTerima = $ttsj->no_tanda_terima ?? $ttsj->nomor_tanda_terima;
                    if ($noTandaTerima) {
                        $prospeks = Prospek::where('no_surat_jalan', $noTandaTerima)->get();
                        foreach ($prospeks as $prospek) {
                            $prospek->update(['kuantitas' => $realTotalKuantitas]);
                            
                            // Update NaikKapal linked to this Prospek
                            NaikKapal::where('prospek_id', $prospek->id)
                                ->update(['kuantitas' => $realTotalKuantitas]);
                                
                            // Update Manifest linked to this Prospek
                            Manifest::where('prospek_id', $prospek->id)
                                ->update(['kuantitas' => $realTotalKuantitas]);
                        }
                    }
                    
                    // Also check if any Manifest directly uses this nomor_tanda_terima but without prospek
                    if ($noTandaTerima) {
                        Manifest::where('nomor_tanda_terima', $noTandaTerima)
                            ->update(['kuantitas' => $realTotalKuantitas]);
                    }
                });

                $this->info("Fixed TTSJ: {$ttsj->no_tanda_terima} (Old: {$oldKuantitas}, New: {$realTotalKuantitas})");
                $fixedCount++;
            }
        }

        $this->info("Finished! Fixed {$fixedCount} records.");
    }
}
