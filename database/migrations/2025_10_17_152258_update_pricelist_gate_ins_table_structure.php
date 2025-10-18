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
        Schema::table('pricelist_gate_ins', function (Blueprint $table) {
            // Add missing columns
            if (!Schema::hasColumn('pricelist_gate_ins', 'catatan')) {
                $table->text('catatan')->nullable()->after('keterangan');
            }
            if (!Schema::hasColumn('pricelist_gate_ins', 'tarif')) {
                $table->decimal('tarif', 15, 2)->default(0)->after('catatan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pricelist_gate_ins', function (Blueprint $table) {
            // Drop new columns if they exist
            if (Schema::hasColumn('pricelist_gate_ins', 'catatan')) {
                $table->dropColumn('catatan');
            }
            if (Schema::hasColumn('pricelist_gate_ins', 'tarif')) {
                $table->dropColumn('tarif');
            }
        });
    }
};
