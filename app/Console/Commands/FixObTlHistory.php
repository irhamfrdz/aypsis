<?php

namespace App\Console\Commands;

use App\Models\Bl;
use App\Models\Gudang;
use App\Models\HistoryKontainer;
use App\Models\Kontainer;
use App\Models\NaikKapal;
use App\Models\StockKontainer;
use Illuminate\Console\Command;

class FixObTlHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ob:fix-tl-history {voyage=BERAUMAS38}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfills missing HistoryKontainer records for containers processed with TL (Tanda Langsung) on a specific voyage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $voyage = $this->argument('voyage');
        $this->info("Scanning for missing TL history logs for voyage: {$voyage}...");

        $muatCount = 0;
        $bongkarCount = 0;

        // 1. Process NaikKapal (Muat)
        $naikKapals = NaikKapal::where('no_voyage', $voyage)
            ->where('sudah_ob', true)
            ->where('is_tl', true)
            ->get();

        $this->info('Found '.$naikKapals->count().' TL Muat records in naik_kapal table.');

        foreach ($naikKapals as $naikKapal) {
            $nomorKontainer = $naikKapal->nomor_kontainer;
            if (! $nomorKontainer) {
                continue;
            }

            // Check if history already exists for this container and voyage/activity
            $exists = HistoryKontainer::where('nomor_kontainer', $nomorKontainer)
                ->where('keterangan', 'like', "%Voyage: {$voyage}%")
                ->exists();

            if ($exists) {
                $this->line("History log for {$nomorKontainer} (Muat) already exists. Skipping.");

                continue;
            }

            // Force 'ke' destination to 'ON BOARD'
            $naikKapal->ke = 'ON BOARD';
            $naikKapal->save();

            $targetGudangId = null;
            $gudang = Gudang::where('nama_gudang', 'ON BOARD')->first();
            if ($gudang) {
                $targetGudangId = $gudang->id;
            }

            // Update container/stock location if target gudang is resolved
            if ($targetGudangId) {
                StockKontainer::where('nomor_seri_gabungan', $nomorKontainer)
                    ->update(['gudangs_id' => $targetGudangId]);

                Kontainer::where('nomor_seri_gabungan', $nomorKontainer)
                    ->update(['gudangs_id' => $targetGudangId]);
            }

            $typeKontainer = Kontainer::where('nomor_seri_gabungan', $nomorKontainer)->exists() ? 'kontainer' : 'stock';

            // Create history
            HistoryKontainer::create([
                'nomor_kontainer' => $nomorKontainer,
                'tipe_kontainer' => $typeKontainer,
                'jenis_kegiatan' => 'Masuk',
                'tanggal_kegiatan' => $naikKapal->tanggal_ob ?? $naikKapal->updated_at ?? now(),
                'gudang_id' => $targetGudangId,
                'keterangan' => 'OB TL ke Kapal: '.($naikKapal->nama_kapal ?? '-').'. Voyage: '.($naikKapal->no_voyage ?? '-'),
                'created_by' => $naikKapal->updated_by ?? 1,
            ]);

            $this->line("Created missing Muat TL history for: {$nomorKontainer}");
            $muatCount++;
        }

        // 2. Process Bl (Bongkar)
        $bls = Bl::where('no_voyage', $voyage)
            ->where('sudah_ob', true)
            ->where('sudah_tl', true)
            ->get();

        $this->info('Found '.$bls->count().' TL Bongkar records in bls table.');

        foreach ($bls as $bl) {
            $nomorKontainer = $bl->nomor_kontainer;
            if (! $nomorKontainer) {
                continue;
            }

            // Check if history already exists for this container and voyage/activity
            $exists = HistoryKontainer::where('nomor_kontainer', $nomorKontainer)
                ->where('keterangan', 'like', "%Voyage: {$voyage}%")
                ->exists();

            if ($exists) {
                $this->line("History log for {$nomorKontainer} (Bongkar) already exists. Skipping.");

                continue;
            }

            // Find gudang
            $targetGudangId = null;
            if ($bl->ke) {
                $gudang = Gudang::where('nama_gudang', $bl->ke)->first();
                if ($gudang) {
                    $targetGudangId = $gudang->id;
                }
            }

            // Update container/stock location if target gudang is resolved
            if ($targetGudangId) {
                StockKontainer::where('nomor_seri_gabungan', $nomorKontainer)
                    ->update(['gudangs_id' => $targetGudangId]);

                Kontainer::where('nomor_seri_gabungan', $nomorKontainer)
                    ->update(['gudangs_id' => $targetGudangId]);
            }

            $typeKontainer = Kontainer::where('nomor_seri_gabungan', $nomorKontainer)->exists() ? 'kontainer' : 'stock';

            // Create history
            HistoryKontainer::create([
                'nomor_kontainer' => $nomorKontainer,
                'tipe_kontainer' => $typeKontainer,
                'jenis_kegiatan' => 'Masuk',
                'tanggal_kegiatan' => $bl->tanggal_ob ?? $bl->updated_at ?? now(),
                'gudang_id' => $targetGudangId,
                'keterangan' => 'OB TL Bongkar (Tanda Langsung) dari Kapal: '.($bl->nama_kapal ?? '-').'. Voyage: '.($bl->no_voyage ?? '-'),
                'created_by' => $bl->updated_by ?? 1,
            ]);

            $this->line("Created missing Bongkar TL history for: {$nomorKontainer}");
            $bongkarCount++;
        }

        $this->info("Repair completed. Added {$muatCount} Muat TL logs and {$bongkarCount} Bongkar TL logs.");

        return 0;
    }
}
