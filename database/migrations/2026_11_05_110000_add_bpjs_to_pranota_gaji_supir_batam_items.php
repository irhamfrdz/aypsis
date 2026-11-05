<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pranota_gaji_supir_batam_items', function (Blueprint $table) {
            $table->decimal('bpjs', 15, 2)->nullable();
            $table->unsignedBigInteger('bpjs_detail_id')->nullable();
            $table->string('bpjs_nomor_pranota', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pranota_gaji_supir_batam_items', function (Blueprint $table) {
            $table->dropColumn(['bpjs', 'bpjs_detail_id', 'bpjs_nomor_pranota']);
        });
    }
};
