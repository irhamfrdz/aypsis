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
        Schema::table('pranota_uang_jalans', function (Blueprint $table) {
            $table->decimal('penyesuaian', 15, 2)->default(0)->after('total_amount');
            $table->text('keterangan_penyesuaian')->nullable()->after('penyesuaian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pranota_uang_jalans', function (Blueprint $table) {
            $table->dropColumn(['penyesuaian', 'keterangan_penyesuaian']);
        });
    }
};