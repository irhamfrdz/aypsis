<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tagihan_lolo_batam_items', function (Blueprint $table) {
            $table->date('tanggal_lolo')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('tagihan_lolo_batam_items', function (Blueprint $table) {
            $table->dropColumn('tanggal_lolo');
        });
    }
};
