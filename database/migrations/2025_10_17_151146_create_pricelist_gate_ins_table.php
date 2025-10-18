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
        Schema::create('pricelist_gate_ins', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pricelist');
            $table->unsignedBigInteger('terminal_id')->nullable();
            $table->unsignedBigInteger('kapal_id')->nullable();
            $table->unsignedBigInteger('service_id')->nullable();
            $table->enum('tipe_kontainer', ['dry', 'reefer', 'flat', 'open_top', 'tank'])->nullable();
            $table->integer('size')->nullable(); // dalam feet, contoh: 20, 40
            $table->decimal('harga_per_kontainer', 15, 2)->default(0);
            $table->decimal('harga_per_teus', 15, 2)->default(0); // TEU = Twenty-foot Equivalent Unit
            $table->enum('mata_uang', ['IDR', 'USD', 'EUR'])->default('IDR');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->text('keterangan')->nullable();
            $table->date('berlaku_dari')->nullable();
            $table->date('berlaku_sampai')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('terminal_id')->references('id')->on('master_terminals')->onDelete('set null');
            $table->foreign('kapal_id')->references('id')->on('master_kapals')->onDelete('set null');
            $table->foreign('service_id')->references('id')->on('master_services')->onDelete('set null');

            // Indexes
            $table->index(['terminal_id', 'kapal_id', 'service_id']);
            $table->index(['tipe_kontainer', 'size']);
            $table->index(['berlaku_dari', 'berlaku_sampai']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricelist_gate_ins');
    }
};
