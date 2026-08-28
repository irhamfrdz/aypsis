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
        Schema::table('master_customer_buruhs', function (Blueprint $table) {
            $table->dropColumn(['pic', 'no_telp', 'alamat', 'keterangan']);
            $table->string('bank')->nullable()->after('nama_customer');
            $table->string('nomor_rekening')->nullable()->after('bank');
            $table->string('penerima')->nullable()->after('nomor_rekening');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_customer_buruhs', function (Blueprint $table) {
            $table->dropColumn(['bank', 'nomor_rekening', 'penerima']);
            $table->string('pic')->nullable();
            $table->string('no_telp')->nullable();
            $table->text('alamat')->nullable();
            $table->text('keterangan')->nullable();
        });
    }
};
