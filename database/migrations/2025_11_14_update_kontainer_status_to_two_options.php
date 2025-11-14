<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update semua status yang bukan "Tersedia" menjadi "Tidak Tersedia"
        DB::table('kontainers')
            ->whereNotIn('status', ['Tersedia'])
            ->update(['status' => 'Tidak Tersedia']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak ada rollback karena kita tidak tahu status aslinya
        // Biarkan status tetap seperti setelah migration
    }
};
