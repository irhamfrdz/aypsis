<?php

use App\Services\ManifestApprovalGoodsSync;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $sync = app(ManifestApprovalGoodsSync::class);

        DB::table('approval_tanda_terima_2_goods')
            ->select(['id', 'source_type', 'source_id'])
            ->orderBy('id')
            ->chunkById(100, function ($overrides) use ($sync): void {
                foreach ($overrides as $override) {
                    $sync->syncSource($override->source_type, (int) $override->source_id);
                }
            });
    }

    public function down(): void
    {
        // Nilai manifest sebelum sinkronisasi tidak dapat dipulihkan secara otomatis.
    }
};
