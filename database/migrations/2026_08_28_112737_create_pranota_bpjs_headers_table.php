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
        Schema::create('pranota_bpjs_headers', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pranota', 100)->unique();
            $table->date('tanggal_pranota');
            $table->integer('periode_bulan');
            $table->integer('periode_tahun');
            $table->integer('total_karyawan')->default(0);
            $table->decimal('total_bpjs_kesehatan', 15, 2)->default(0);
            $table->decimal('total_bpjs_ketenagakerjaan', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->string('status', 30)->default('draft');
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pranota_bpjs_headers');
    }
};
