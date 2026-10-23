<?php

namespace App\Console\Commands;

use App\Models\Asset;
use App\Models\Mobil;
use App\Services\MobilAssetSyncService;
use Illuminate\Console\Command;

class SyncMobilAssets extends Command
{
    protected $signature = 'asset:sync-mobils';

    protected $description = 'Sinkronkan mobil dengan switch asset karyawan aktif ke Master Asset';

    public function handle(MobilAssetSyncService $service): int
    {
        $count = 0;
        Mobil::where(function ($query) {
            $query->where('is_asset_karyawan', 1)->orWhereIn('id', Asset::withTrashed()
                ->where('source_table', 'mobils')->select('source_id'));
        })->chunkById(100, function ($mobils) use ($service, &$count) {
            foreach ($mobils as $mobil) {
                $service->sync($mobil);
                $count++;
            }
        });
        $this->info($count.' data mobil disinkronkan ke Master Asset.');

        return self::SUCCESS;
    }
}
