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
        Schema::table('invoice_aktivitas_lain', function (Blueprint $table) {
            if (!Schema::hasColumn('invoice_aktivitas_lain', 'jumlah_retur')) {
                $table->integer('jumlah_retur')->nullable()->after('jenis_penyesuaian')->comment('Jumlah retur galon untuk jenis penyesuaian retur galon');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_aktivitas_lain', function (Blueprint $table) {
            if (Schema::hasColumn('invoice_aktivitas_lain', 'jumlah_retur')) {
                $table->dropColumn('jumlah_retur');
            }
        });
    }
};
