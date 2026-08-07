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
        if (!Schema::hasTable('pranota_lembur_karyawans')) {
            Schema::create('pranota_lembur_karyawans', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('pranota_lembur_karyawan_header_id')->nullable();
                $table->unsignedBigInteger('karyawan_id');
                $table->string('jam_lembur')->nullable();
                $table->decimal('nominal_awal', 15, 2);
                $table->decimal('adjustment', 15, 2)->default(0);
                $table->decimal('total_akhir', 15, 2);
                $table->text('catatan')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pranota_lembur_karyawans');
    }
};
