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
        // sqlite does not support dropping columns cleanly; skip in tests
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        if (Schema::hasTable('tujuans') && Schema::hasColumn('tujuans', 'nama_tujuan')) {
            Schema::table('tujuans', function (Blueprint $table) {
                $table->dropColumn('nama_tujuan');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tujuans') && !Schema::hasColumn('tujuans', 'nama_tujuan')) {
            Schema::table('tujuans', function (Blueprint $table) {
                $table->string('nama_tujuan')->nullable();
            });
        }
    }
};
