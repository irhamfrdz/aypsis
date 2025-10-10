<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pembayaran_dp_obs', function (Blueprint $table) {
            // Check if column doesn't exist before adding
            if (!Schema::hasColumn('pembayaran_dp_obs', 'status')) {
                $table->enum('status', ['dp_belum_terpakai', 'dp_terpakai'])->default('dp_belum_terpakai')->after('keterangan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_dp_obs', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
