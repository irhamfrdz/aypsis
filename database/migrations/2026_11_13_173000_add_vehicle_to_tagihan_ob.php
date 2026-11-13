<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('tagihan_ob', function (Blueprint $table) {
            $table->unsignedBigInteger('mobil_id')->nullable();
            $table->string('nomor_polisi')->nullable();
        });
    }
    public function down(): void {
        Schema::table('tagihan_ob', function (Blueprint $table) {
            $table->dropColumn(['mobil_id', 'nomor_polisi']);
        });
    }
};
