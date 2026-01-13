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
        Schema::table('biaya_kapals', function (Blueprint $table) {
            $table->decimal('ppn', 15, 2)->nullable()->after('pph_dokumen')->comment('PPN untuk Biaya Penumpukan');
            $table->decimal('pph', 15, 2)->nullable()->after('ppn')->comment('PPH untuk Biaya Penumpukan');
            $table->decimal('total_biaya', 15, 2)->nullable()->after('pph')->comment('Total Biaya untuk Biaya Penumpukan (Nominal + PPN - PPH)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_kapals', function (Blueprint $table) {
            $table->dropColumn(['ppn', 'pph', 'total_biaya']);
        });
    }
};
