<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! DB::table('nama_stock_bans')->where('nama', 'ACCU GS N200')->exists()) {
            DB::table('nama_stock_bans')->insert([
                'nama' => 'ACCU GS N200',
                'status' => 'active',
                'kuantitas' => 0,
                'type' => 'UNIT',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Keep the master item so existing stock records remain linked to it.
    }
};
