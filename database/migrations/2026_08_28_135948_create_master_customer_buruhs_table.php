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
        Schema::create('master_customer_buruhs', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama_customer');
            $table->string('pic')->nullable();
            $table->string('no_telp')->nullable();
            $table->text('alamat')->nullable();
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_customer_buruhs');
    }
};
