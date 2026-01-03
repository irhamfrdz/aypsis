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
        if (Schema::hasColumn('invoice_aktivitas_lain', 'jumlah_retur')) {
            Schema::table('invoice_aktivitas_lain', function (Blueprint $table) {
                $table->dropColumn('jumlah_retur');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('invoice_aktivitas_lain', 'jumlah_retur')) {
            Schema::table('invoice_aktivitas_lain', function (Blueprint $table) {
                $table->integer('jumlah_retur')->nullable()->after('jenis_penyesuaian')->comment('Jumlah retur galon untuk jenis penyesuaian retur galon');
            });
        }
    }
};
