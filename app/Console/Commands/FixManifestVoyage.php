<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NaikKapal;
use App\Models\Manifest;
use App\Models\TandaTerimaLclKontainerPivot;
use Illuminate\Support\Facades\DB;

class FixManifestVoyage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:manifest-voyage {voyage=SA01JP26}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix missing manifests for a specific voyage by generating them from NaikKapal data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $voyage = $this->argument('voyage');
        $this->info("Starting manifest fix for voyage: {$voyage}");

        // 1. Get all NaikKapal records for this voyage
        // Normalize search to handle case sensitivity if needed, but strict for now based on user request
        $naikKapals = NaikKapal::where('no_voyage', $voyage)->get();

        $this->info("Found " . $naikKapals->count() . " NaikKapal records for voyage {$voyage}.");

        $stats = [
            'created' => 0,
            'skipped' => 0,
            'errors' => 0,
            'manifests_generated' => 0
        ];

        foreach ($naikKapals as $nk) {
            try {
                $this->processNaikKapal($nk, $stats);
            } catch (\Exception $e) {
                $this->error("Error processing container {$nk->nomor_kontainer}: " . $e->getMessage());
                $stats['errors']++;
            }
        }

        $this->info("Processing complete.");
        $this->info("Records Processed: " . $naikKapals->count());
        $this->info("Manifests Created: " . $stats['manifests_generated']);
        $this->info("Skipped (Already Exists): " . $stats['skipped']);
        $this->info("Errors: " . $stats['errors']);
    }

    private function processNaikKapal($naikKapal, &$stats)
    {
        // Logic adapted from ObController::markAsOB
        
        // PENTING: User request specifically asked to handle LCL Tanda Terima counts
        if (strtoupper($naikKapal->tipe_kontainer) === 'LCL') {
            $this->comment("Processing LCL container: {$naikKapal->nomor_kontainer}");
            
            // Search for Tanda Terima connected to this container
            $tandaTerimaRecords = TandaTerimaLclKontainerPivot::where('nomor_kontainer', $naikKapal->nomor_kontainer)
                ->with('tandaTerima.items')
                ->get();
            
            if ($tandaTerimaRecords->count() > 0) {
                $this->line(" - Found {$tandaTerimaRecords->count()} Tanda Terima records.");
                
                foreach ($tandaTerimaRecords as $pivot) {
                    $tandaTerima = $pivot->tandaTerima;
                    if (!$tandaTerima) continue;

                    // CHECK IF EXISTS
                    $exists = Manifest::where('nomor_kontainer', $naikKapal->nomor_kontainer)
                        ->where('no_voyage', $naikKapal->no_voyage)
                        ->where('nomor_tanda_terima', $tandaTerima->nomor_tanda_terima)
                        ->exists();

                    if ($exists) {
                        $stats['skipped']++;
                        continue;
                    }

                    // Create Manifest
                    $manifest = new Manifest();
                    
                    // Container Data
                    $manifest->nomor_kontainer = $naikKapal->nomor_kontainer;
                    $manifest->no_seal = $pivot->nomor_seal ?? $naikKapal->no_seal;
                    $manifest->tipe_kontainer = $naikKapal->tipe_kontainer;
                    $manifest->size_kontainer = $naikKapal->size_kontainer;
                    
                    // Ship & Voyage
                    $manifest->nama_kapal = $naikKapal->nama_kapal;
                    $manifest->no_voyage = $naikKapal->no_voyage;
                    
                    // Tanda Terima Data
                    $manifest->nomor_tanda_terima = $tandaTerima->nomor_tanda_terima;
                    $manifest->pengirim = $tandaTerima->nama_pengirim;
                    $manifest->penerima = $tandaTerima->nama_penerima;
                    $manifest->alamat_pengirim = $tandaTerima->alamat_pengirim;
                    $manifest->alamat_penerima = $tandaTerima->alamat_penerima;
                    
                    // Items/Goods
                    $namaBarang = $tandaTerima->items->pluck('nama_barang')->filter()->implode(', ');
                    $manifest->nama_barang = $namaBarang ?: $naikKapal->jenis_barang;
                    
                    // Stats
                    $manifest->volume = $tandaTerima->items->sum('meter_kubik');
                    $manifest->tonnage = $tandaTerima->items->sum('tonase');
                    
                    // Ports
                    $manifest->pelabuhan_muat = $naikKapal->asal_kontainer;
                    $manifest->pelabuhan_bongkar = $naikKapal->ke;
                    
                    // Dates
                    $manifest->tanggal_berangkat = $naikKapal->tanggal_muat ?? now();
                    $manifest->penerimaan = $tandaTerima->tanggal_tanda_terima;
                    
                    // Generate Nomor BL
                    $manifest->nomor_bl = $this->generateNomorBl();
                    
                    // Prospek Reference
                    if ($naikKapal->prospek_id) {
                        $manifest->prospek_id = $naikKapal->prospek_id;
                    }
                    
                    // Default values
                    $manifest->created_by = 1; // System/Admin
                    $manifest->updated_by = 1;
                    
                    $manifest->save();
                    $stats['manifests_generated']++;
                    $this->info("   + Created Manifest {$manifest->nomor_bl} for TT {$tandaTerima->nomor_tanda_terima}");
                }
            } else {
                // Fallback LCL if no Tanda Terima found (treat as single record)
                $this->line(" - No Tanda Terima found for LCL, creating single fallback manifest.");
                $this->createSingleManifest($naikKapal, $stats);
            }
        } else {
            // FCL or CARGO
            $this->createSingleManifest($naikKapal, $stats);
        }
    }

    private function createSingleManifest($naikKapal, &$stats)
    {
        // CHECK IF EXISTS (Strict check for single manifest type)
        $exists = Manifest::where('nomor_kontainer', $naikKapal->nomor_kontainer)
            ->where('no_voyage', $naikKapal->no_voyage)
            ->exists(); // Note: loose check might be better? No, strict is safer to avoid dupes.

        if ($exists) {
            $stats['skipped']++;
            return;
        }

        $manifest = new Manifest();
        $manifest->nomor_kontainer = $naikKapal->nomor_kontainer;
        $manifest->no_seal = $naikKapal->no_seal;
        $manifest->tipe_kontainer = $naikKapal->tipe_kontainer;
        $manifest->size_kontainer = $naikKapal->size_kontainer;
        $manifest->nama_kapal = $naikKapal->nama_kapal;
        $manifest->no_voyage = $naikKapal->no_voyage;
        $manifest->nama_barang = $naikKapal->jenis_barang;
        $manifest->volume = $naikKapal->total_volume;
        $manifest->tonnage = $naikKapal->total_tonase;
        $manifest->pelabuhan_muat = $naikKapal->asal_kontainer;
        $manifest->pelabuhan_bongkar = $naikKapal->ke;
        $manifest->tanggal_berangkat = $naikKapal->tanggal_muat ?? now();

        if ($naikKapal->prospek_id && $naikKapal->prospek) {
            $manifest->prospek_id = $naikKapal->prospek_id;
            $manifest->pengirim = $naikKapal->prospek->pt_pengirim;
            $manifest->penerima = $naikKapal->prospek->tujuan_pengiriman;
        }

        $manifest->nomor_bl = $this->generateNomorBl();
        $manifest->created_by = 1;
        $manifest->updated_by = 1;

        $manifest->save();
        $stats['manifests_generated']++;
        $this->info(" + Created Manifest {$manifest->nomor_bl} for {$naikKapal->nomor_kontainer}");
    }

    private function generateNomorBl()
    {
        $lastManifest = Manifest::whereNotNull('nomor_bl')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastManifest && $lastManifest->nomor_bl) {
            preg_match('/\d+/', $lastManifest->nomor_bl, $matches);
            $lastNumber = isset($matches[0]) ? intval($matches[0]) : 0;
            $nextNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
            return 'MNF-' . $nextNumber;
        } else {
            return 'MNF-000001';
        }
    }
}
