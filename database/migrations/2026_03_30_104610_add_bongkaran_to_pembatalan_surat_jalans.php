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
        Schema::table('pembatalan_surat_jalans', function (Blueprint $table) {
            $table->unsignedBigInteger('surat_jalan_id')->nullable()->change();
            $table->unsignedBigInteger('surat_jalan_bongkaran_id')->nullable()->after('surat_jalan_id');
            $table->string('tipe_sj')->default('reguler')->after('surat_jalan_bongkaran_id'); // reguler, bongkaran
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembatalan_surat_jalans', function (Blueprint $table) {
            $table->unsignedBigInteger('surat_jalan_id')->change();
            $table->dropColumn(['surat_jalan_bongkaran_id', 'tipe_sj']);
        });
    }
};
