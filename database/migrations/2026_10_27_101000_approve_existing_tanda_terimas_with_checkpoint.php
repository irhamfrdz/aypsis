<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('tanda_terimas')
            ->where('approval_pelabuhan_status', 'pending')
            ->whereNotNull('tanggal_checkpoint_supir')
            ->update(['approval_pelabuhan_status' => 'approved']);
    }

    public function down(): void
    {
        // Status lama tidak dapat dibedakan dari persetujuan manual setelah migrasi.
    }
};
