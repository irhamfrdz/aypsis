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
        Schema::table('vendor_asuransi', function (Blueprint $table) {
            $table->decimal('tarif', 5, 2)->nullable()->default(0)->after('nama_asuransi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendor_asuransi', function (Blueprint $table) {
            $table->dropColumn('tarif');
        });
    }
};
