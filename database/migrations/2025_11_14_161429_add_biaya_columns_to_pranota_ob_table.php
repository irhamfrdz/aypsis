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
        Schema::table('pranota_ob', function (Blueprint $table) {
            // Rename total_amount ke total_biaya untuk konsistensi
            $table->renameColumn('total_amount', 'total_biaya');
            
            // Tambah kolom penyesuaian dan grand_total
            $table->decimal('penyesuaian', 15, 2)->default(0)->after('total_biaya');
            $table->decimal('grand_total', 15, 2)->default(0)->after('penyesuaian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pranota_ob', function (Blueprint $table) {
            $table->renameColumn('total_biaya', 'total_amount');
            $table->dropColumn(['penyesuaian', 'grand_total']);
        });
    }
};
