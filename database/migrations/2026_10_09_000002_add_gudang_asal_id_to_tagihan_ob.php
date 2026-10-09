<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tagihan_ob', function (Blueprint $table) {
            $table->foreignId('gudang_asal_id')->nullable()->constrained('gudangs')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tagihan_ob', function (Blueprint $table) {
            $table->dropConstrainedForeignId('gudang_asal_id');
        });
    }
};
