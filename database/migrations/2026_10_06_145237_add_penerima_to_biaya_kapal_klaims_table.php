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
        if (Schema::hasTable('biaya_kapal_klaims') && ! Schema::hasColumn('biaya_kapal_klaims', 'penerima')) {
            Schema::table('biaya_kapal_klaims', function (Blueprint $table) {
                $table->string('penerima')->nullable()->after('vendor');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('biaya_kapal_klaims') && Schema::hasColumn('biaya_kapal_klaims', 'penerima')) {
            Schema::table('biaya_kapal_klaims', function (Blueprint $table) {
                $table->dropColumn('penerima');
            });
        }
    }
};
