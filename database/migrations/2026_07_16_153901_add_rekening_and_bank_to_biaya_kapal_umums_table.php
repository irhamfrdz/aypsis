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
        Schema::table('biaya_kapal_umums', function (Blueprint $table) {
            $table->string('nomor_rekening')->nullable()->after('penerima');
            $table->foreignId('bank_id')->nullable()->after('nomor_rekening')->constrained('banks')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_kapal_umums', function (Blueprint $table) {
            $table->dropForeign(['bank_id']);
            $table->dropColumn(['nomor_rekening', 'bank_id']);
        });
    }
};
