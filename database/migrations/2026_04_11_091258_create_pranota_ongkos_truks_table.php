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
        // 0. Drop existing tables if they exist from failed attempts
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('pranota_ongkos_truk_items');
        Schema::dropIfExists('pranota_ongkos_truks');
        Schema::enableForeignKeyConstraints();

        // 1. Create parent table first
        Schema::create('pranota_ongkos_truks', function (Blueprint $table) {
            $table->id();
            $table->string('no_pranota')->unique();
            $table->date('tanggal_pranota');
            $table->unsignedBigInteger('supir_id')->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->decimal('total_nominal', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->string('status')->default('draft');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        // 2. Create items table second
        Schema::create('pranota_ongkos_truk_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pranota_ongkos_truk_id');
            $table->unsignedBigInteger('surat_jalan_id')->nullable();
            $table->unsignedBigInteger('surat_jalan_bongkaran_id')->nullable();
            $table->string('no_surat_jalan')->nullable();
            $table->date('tanggal')->nullable();
            $table->decimal('nominal', 15, 2)->default(0);
            $table->string('type')->nullable(); // regular, regular_adj, bongkaran, bongkaran_adj
            $table->timestamps();
        });

        // 3. Add foreign keys last to avoid ordering issues
        Schema::table('pranota_ongkos_truks', function (Blueprint $table) {
            $table->foreign('supir_id')->references('id')->on('karyawans')->onDelete('set null');
            $table->foreign('vendor_id')->references('id')->on('vendor_supirs')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('pranota_ongkos_truk_items', function (Blueprint $table) {
            $table->foreign('pranota_ongkos_truk_id', 'fk_pot_items_parent')->references('id')->on('pranota_ongkos_truks')->onDelete('cascade');
            $table->foreign('surat_jalan_id')->references('id')->on('surat_jalans')->onDelete('set null');
            $table->foreign('surat_jalan_bongkaran_id', 'fk_pot_items_sjb')->references('id')->on('surat_jalan_bongkarans')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pranota_ongkos_truk_items');
        Schema::dropIfExists('pranota_ongkos_truks');
    }
};
