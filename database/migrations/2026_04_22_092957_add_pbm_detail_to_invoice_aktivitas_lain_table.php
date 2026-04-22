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
            if (!Schema::hasColumn('invoice_aktivitas_lain', 'nomor_bank')) {
                $table->string('nomor_bank')->nullable()->after('total');
            }
            if (!Schema::hasColumn('invoice_aktivitas_lain', 'nominal_bayar')) {
                $table->decimal('nominal_bayar', 15, 2)->default(0)->after('nomor_bank');
            }
            if (!Schema::hasColumn('invoice_aktivitas_lain', 'biaya_admin')) {
                $table->decimal('biaya_admin', 15, 2)->default(0)->after('nominal_bayar');
            }
            $table->longText('pbm_detail')->nullable()->after('total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_aktivitas_lain', function (Blueprint $table) {
            $table->dropColumn(['nomor_bank', 'nominal_bayar', 'biaya_admin', 'pbm_detail']);
        });
    }
};
