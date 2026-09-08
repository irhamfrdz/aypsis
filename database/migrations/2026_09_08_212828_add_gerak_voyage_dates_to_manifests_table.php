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
        Schema::table('manifests', function (Blueprint $table) {
            $table->date('tanggal_mulai_berlayar')->nullable();
            $table->date('tanggal_berlabuh')->nullable();
            $table->date('tanggal_sandar')->nullable();
            $table->date('tanggal_mulai_bongkar')->nullable();
            $table->date('tanggal_selesai_bongkar')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manifests', function (Blueprint $table) {
            $table->dropColumn([
                'tanggal_mulai_berlayar',
                'tanggal_berlabuh',
                'tanggal_sandar',
                'tanggal_mulai_bongkar',
                'tanggal_selesai_bongkar',
            ]);
        });
    }
};
