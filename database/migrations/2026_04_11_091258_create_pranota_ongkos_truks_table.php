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
            
            $table->foreign('supir_id')->references('id')->on('karyawans')->onDelete('set null');
            $table->foreign('vendor_id')->references('id')->on('vendor_supirs')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pranota_ongkos_truks');
    }
};
