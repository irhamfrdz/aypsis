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
        Schema::create('master_kartu_bensin_batam_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('master_kartu_bensin_batam_id');
            $table->dateTime('tanggal');
            $table->string('tipe'); // 'bertambah', 'berkurang'
            $table->decimal('nominal', 15, 2);
            $table->decimal('saldo_sebelum', 15, 2);
            $table->decimal('saldo_sesudah', 15, 2);
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('master_kartu_bensin_batam_id', 'mkbbh_card_id_foreign')
                ->references('id')
                ->on('master_kartu_bensin_batams')
                ->onDelete('cascade');

            $table->foreign('created_by', 'mkbbh_user_id_foreign')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_kartu_bensin_batam_histories');
    }
};
