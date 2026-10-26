<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biaya_kapal_meratus', function (Blueprint $table) {
            $table->boolean('is_storage')->default(false);
            $table->date('tanggal_mulai_storage')->nullable();
            $table->date('tanggal_selesai_storage')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('biaya_kapal_meratus', function (Blueprint $table) {
            $table->dropColumn(['is_storage', 'tanggal_mulai_storage', 'tanggal_selesai_storage']);
        });
    }
};
