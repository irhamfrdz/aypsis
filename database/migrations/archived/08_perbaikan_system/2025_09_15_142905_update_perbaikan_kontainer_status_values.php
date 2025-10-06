<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing status values to new ones
        DB::table('perbaikan_kontainers')->where('status_perbaikan', 'pending')->update(['status_perbaikan' => 'belum_masuk_pranota']);
        DB::table('perbaikan_kontainers')->where('status_perbaikan', 'in_progress')->update(['status_perbaikan' => 'sudah_masuk_pranota']);
        DB::table('perbaikan_kontainers')->where('status_perbaikan', 'completed')->update(['status_perbaikan' => 'sudah_dibayar']);
        DB::table('perbaikan_kontainers')->where('status_perbaikan', 'cancelled')->update(['status_perbaikan' => 'belum_masuk_pranota']);

        // Update the default value in the table schema
        Schema::table('perbaikan_kontainers', function (Blueprint $table) {
            $table->string('status_perbaikan')->default('belum_masuk_pranota')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert status values back to old ones
        DB::table('perbaikan_kontainers')->where('status_perbaikan', 'belum_masuk_pranota')->update(['status_perbaikan' => 'pending']);
        DB::table('perbaikan_kontainers')->where('status_perbaikan', 'sudah_masuk_pranota')->update(['status_perbaikan' => 'in_progress']);
        DB::table('perbaikan_kontainers')->where('status_perbaikan', 'sudah_dibayar')->update(['status_perbaikan' => 'completed']);

        // Revert the default value
        Schema::table('perbaikan_kontainers', function (Blueprint $table) {
            $table->string('status_perbaikan')->default('pending')->change();
        });
    }
};
