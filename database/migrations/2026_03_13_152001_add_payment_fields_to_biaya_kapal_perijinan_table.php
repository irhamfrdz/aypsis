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
        Schema::table('biaya_kapal_perijinan', function (Blueprint $table) {
            $table->string('lokasi')->nullable()->after('vendor');
            $table->decimal('sub_total', 15, 2)->default(0)->after('biaya_pbni');
            $table->decimal('pph', 15, 2)->default(0)->after('sub_total');
            $table->decimal('grand_total', 15, 2)->default(0)->after('pph');
            $table->string('penerima')->nullable()->after('grand_total');
            $table->string('nomor_rekening')->nullable()->after('penerima');
            $table->date('tanggal_invoice_vendor')->nullable()->after('nomor_rekening');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_kapal_perijinan', function (Blueprint $table) {
            $table->dropColumn([
                'lokasi',
                'sub_total',
                'pph',
                'grand_total',
                'penerima',
                'nomor_rekening',
                'tanggal_invoice_vendor'
            ]);
        });
    }
};
