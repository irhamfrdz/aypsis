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
        Schema::table('realisasi_uang_muka', function (Blueprint $table) {
            // Change jumlah_per_supir from DECIMAL to JSON to store array of amounts per item
            $table->json('jumlah_per_supir')->change();

            // Add dp_amount column if not exists
            if (!Schema::hasColumn('realisasi_uang_muka', 'dp_amount')) {
                $table->decimal('dp_amount', 15, 2)->default(0)->after('total_pembayaran');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('realisasi_uang_muka', function (Blueprint $table) {
            // Revert back to DECIMAL (this will lose data!)
            $table->decimal('jumlah_per_supir', 15, 2)->change();

            // Drop dp_amount if exists
            if (Schema::hasColumn('realisasi_uang_muka', 'dp_amount')) {
                $table->dropColumn('dp_amount');
            }
        });
    }
};
