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
        Schema::table('master_pricelist_vendor_supirs', function (Blueprint $table) {
            $table->unsignedBigInteger('tujuan_id')->nullable();
            $table->string('jenis_kontainer')->nullable();
            $table->decimal('nominal', 15, 2)->nullable();
            $table->enum('status', ['aktif', 'non-aktif'])->default('aktif');
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('tujuan_id')->references('id')->on('tujuans')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_pricelist_vendor_supirs', function (Blueprint $table) {
            $table->dropForeign(['tujuan_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropColumn(['tujuan_id', 'jenis_kontainer', 'nominal', 'status', 'keterangan', 'created_by', 'updated_by']);
        });
    }
};
