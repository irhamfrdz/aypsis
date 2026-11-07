<?php

use App\Services\StockBanUsageHistoryService;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        (new StockBanUsageHistoryService)->backfill();
    }

    public function down(): void
    {
        // Keep recovered historical usage records.
    }
};
