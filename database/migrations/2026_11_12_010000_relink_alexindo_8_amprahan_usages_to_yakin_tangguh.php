<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $targetIds = DB::table('master_kapals')
            ->whereNull('deleted_at')
            ->whereRaw('UPPER(TRIM(nama_kapal)) = ?', ['KM YAKIN TANGGUH'])
            ->pluck('id');

        if ($targetIds->count() !== 1) {
            return;
        }

        $oldIds = DB::table('master_kapals')
            ->whereIn('nama_kapal', ['KM. ALEXINDO 8', 'KM ALEXINDO 8'])
            ->pluck('id');

        if ($oldIds->isNotEmpty()) {
            DB::table('stock_amprahan_usages')
                ->whereIn('kapal_id', $oldIds)
                ->update(['kapal_id' => $targetIds->first()]);
        }
    }

    public function down(): void
    {
        // Existing Yakin Tangguh usages cannot be distinguished from relinked ones.
    }
};
