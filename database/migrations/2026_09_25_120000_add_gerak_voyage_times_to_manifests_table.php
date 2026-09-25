<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('manifests', function (Blueprint $table) {
            $table->time('jam_muat')->nullable();
            $table->time('jam_mulai_berlayar')->nullable();
            $table->time('jam_berlabuh')->nullable();
            $table->time('jam_sandar')->nullable();
            $table->time('jam_mulai_bongkar')->nullable();
            $table->time('jam_selesai_bongkar')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('manifests', function (Blueprint $table) {
            $table->dropColumn([
                'jam_muat',
                'jam_mulai_berlayar',
                'jam_berlabuh',
                'jam_sandar',
                'jam_mulai_bongkar',
                'jam_selesai_bongkar',
            ]);
        });
    }
};
