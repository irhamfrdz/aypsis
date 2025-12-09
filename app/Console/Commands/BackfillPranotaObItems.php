<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\PranotaOb;
use App\Models\PranotaObItem;

class BackfillPranotaObItems extends Command
{
    protected $signature = 'pranota-ob:backfill-items {--force}';
    protected $description = 'Backfill pranota_ob_items from existing pranota_obs.items JSON';

    public function handle()
    {
        $items = PranotaOb::whereNotNull('items')->get();
        $progress = $this->output->createProgressBar($items->count());
        $progress->start();
        $created = 0;

        foreach ($items as $p) {
            $data = $p->items;
            if (!is_array($data) || count($data) === 0) {
                $progress->advance();
                continue;
            }
            foreach ($data as $it) {
                // Skip if pivot exists
                $exists = PranotaObItem::where('pranota_ob_id', $p->id)
                    ->where('item_id', $it['id'] ?? null)
                    ->where('item_type', isset($it['type']) ? ($it['type'] === 'bl' ? \App\Models\Bl::class : ($it['type'] === 'naik_kapal' ? \App\Models\NaikKapal::class : $it['type'])) : null)
                    ->exists();
                if ($exists) continue;

                $itemType = null;
                if (isset($it['type'])) {
                    if ($it['type'] === 'bl') $itemType = \App\Models\Bl::class;
                    elseif ($it['type'] === 'naik_kapal') $itemType = \App\Models\NaikKapal::class;
                    elseif ($it['type'] === 'tagihan_ob') $itemType = \App\Models\TagihanOb::class;
                }

                PranotaObItem::create([
                    'pranota_ob_id' => $p->id,
                    'item_type' => $itemType,
                    'item_id' => $it['id'] ?? null,
                    'nomor_kontainer' => $it['nomor_kontainer'] ?? null,
                    'nama_barang' => $it['nama_barang'] ?? ($it['jenis_barang'] ?? null),
                    'supir' => $it['supir'] ?? null,
                    'size' => $it['size'] ?? ($it['size_kontainer'] ?? null),
                    'biaya' => $it['biaya'] ?? null,
                ]);
                $created++;
            }
            $progress->advance();
        }

        $progress->finish();
        $this->info("\nCreated {$created} pivot rows.");
        return 0;
    }
}
