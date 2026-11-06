<?php

namespace App\Traits;

use App\Services\StockBanUsageHistoryService;

trait RecordsStockBanUsage
{
    protected static function bootRecordsStockBanUsage(): void
    {
        static::created(function ($ban) {
            if ($ban->status === 'Terpakai') {
                app(StockBanUsageHistoryService::class)->record($ban);
            }
        });

        static::updated(function ($ban) {
            if ($ban->status === 'Terpakai' && $ban->isDirty('status') && $ban->getOriginal('status') !== 'Terpakai') {
                app(StockBanUsageHistoryService::class)->record($ban);
            }
        });
    }
}
