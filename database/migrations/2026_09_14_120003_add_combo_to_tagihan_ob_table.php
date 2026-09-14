<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tagihan_ob', function (Blueprint $table) {
            $table->boolean('is_combo')->default(false)->after('status_kontainer');
        });
    }

    public function down(): void
    {
        Schema::table('tagihan_ob', function (Blueprint $table) {
            $table->dropColumn('is_combo');
        });
    }
};
