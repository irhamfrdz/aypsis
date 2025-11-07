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
        Schema::table('uang_jalans', function (Blueprint $table) {
            // Drop legacy fields that are no longer used in the new form
            $table->dropColumn([
                'jumlah_uang_supir',
                'jumlah_uang_kenek', 
                'total_uang_jalan',
                'keterangan'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uang_jalans', function (Blueprint $table) {
            // Re-add legacy fields if rollback is needed
            $table->decimal('jumlah_uang_supir', 12, 2)->default(0)->after('jumlah_total');
            $table->decimal('jumlah_uang_kenek', 12, 2)->default(0)->after('jumlah_uang_supir');
            $table->decimal('total_uang_jalan', 12, 2)->default(0)->after('jumlah_uang_kenek');
            $table->text('keterangan')->nullable()->after('total_uang_jalan');
        });
    }
};
