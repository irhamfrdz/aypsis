<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->text('spesifikasi')->nullable();
            $table->decimal('kuantitas', 12, 2)->nullable();
            $table->string('satuan', 50)->nullable();
            $table->date('tanggal_kadaluarsa')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn(['spesifikasi', 'kuantitas', 'satuan', 'tanggal_kadaluarsa']);
        });
    }
};
