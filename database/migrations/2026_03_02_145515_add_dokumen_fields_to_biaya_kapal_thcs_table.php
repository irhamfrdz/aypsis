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
        Schema::table('biaya_kapal_thcs', function (Blueprint $table) {
            $table->json('kontainer_ids')->nullable()->after('tanda_terima_ids');
            $table->decimal('biaya_dokumen_muat', 18, 2)->default(0)->after('subtotal');
            $table->decimal('biaya_dokumen_bongkar', 18, 2)->default(0)->after('biaya_dokumen_muat');
            $table->decimal('biaya_materai', 18, 2)->default(0)->after('biaya_dokumen_bongkar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_kapal_thcs', function (Blueprint $table) {
            $table->dropColumn(['kontainer_ids', 'biaya_dokumen_muat', 'biaya_dokumen_bongkar', 'biaya_materai']);
        });
    }
};
