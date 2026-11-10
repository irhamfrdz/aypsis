<?php

namespace App\Console\Commands;

use App\Services\PemakaianBanSyncService;
use Illuminate\Console\Command;

class BackfillPemakaianBan extends Command
{
    protected $signature = 'ban:backfill-pemakaian {--dry-run : Validate and count without writing}';
    protected $description = 'Copy legacy tire and component usage without deleting source data';

    public function handle(PemakaianBanSyncService $service): int
    {
        $result = $service->run((bool) $this->option('dry-run'));
        $this->table(['Jenis komponen', 'Transaksi sumber'], collect($result['counts'])->map(fn ($count, $kind) => [$kind, $count])->values()->all());
        $this->info("New: {$result['inserted']}; already present: {$result['existing']}");
        return self::SUCCESS;
    }
}
