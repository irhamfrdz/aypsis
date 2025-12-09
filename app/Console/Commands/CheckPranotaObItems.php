<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\PranotaOb;

class CheckPranotaObItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pranota-ob:check-items';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lists Pranota OB records with empty or missing items and outputs some metadata to help debugging';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Scanning pranota_obs table for missing or empty items...');

        $rows = DB::table('pranota_obs')
              ->select('id', 'nomor_pranota', 'nama_kapal', 'no_voyage', 'created_by', 'items', 'created_at')
              ->get();

        $emptyCount = 0;
        $badRows = [];
        foreach ($rows as $r) {
            // items might be JSON or NULL
            $items = $r->items;
            $decoded = null;
            try {
                $decoded = is_string($items) ? json_decode($items, true) : $items;
            } catch (\Throwable $e) {
                $decoded = null;
            }

            if (!is_array($decoded) || count($decoded) === 0) {
                $emptyCount++;
                $badRows[] = [
                    'id' => $r->id,
                    'nomor_pranota' => $r->nomor_pranota,
                    'nama_kapal' => $r->nama_kapal,
                    'no_voyage' => $r->no_voyage,
                    'created_by' => $r->created_by,
                    'created_at' => $r->created_at,
                    'raw_items' => $items,
                ];
            }
        }

        if ($emptyCount === 0) {
            $this->info('All pranota_obs records have items.');
            return 0;
        }

        $this->warn("Found {$emptyCount} pranota_obs with empty or missing items:");
        $this->table(['id','nomor_pranota','nama_kapal','no_voyage','created_by','created_at'], $badRows);

        $exportPath = storage_path('app/pranota-ob-empty-items.json');
        file_put_contents($exportPath, json_encode($badRows, JSON_PRETTY_PRINT));
        $this->info("Exported list to: {$exportPath}");

        $this->info('Done.');
        return 0;
    }
}
