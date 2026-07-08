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
        Schema::create('master_chasis_batams', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 50)->unique()->comment('Kode Chasis');
            $table->string('plat_nomor', 50)->nullable()->comment('Nomor Polisi / Plat Chasis');
            $table->string('tipe', 50)->nullable()->comment('Tipe Chasis e.g. 20ft, 40ft');
            $table->string('merek', 100)->nullable()->comment('Merek Chasis');
            $table->integer('tahun_pembuatan')->nullable()->comment('Tahun Pembuatan');
            $table->text('catatan')->nullable()->comment('Catatan tambahan');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif')->comment('Status Chasis');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('kode');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_chasis_batams');
    }
};
