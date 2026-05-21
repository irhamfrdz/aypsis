<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        \Illuminate\Support\Facades\DB::table('karyawans')
            ->where('cabang', 'JKT')
            ->update(['cabang' => 'JAKARTA']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::table('karyawans')
            ->where('cabang', 'JAKARTA')
            ->update(['cabang' => 'JKT']);
    }
};
