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
            ->where('cabang', 'batam')
            ->update(['cabang' => 'BATAM']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::table('karyawans')
            ->where('cabang', 'BATAM')
            ->update(['cabang' => 'batam']);
    }
};
