<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tanda_terimas', function (Blueprint $table) {
            $table->string('nomor_tanda_terima')->nullable()->unique();
        });
    }

    public function down(): void
    {
        Schema::table('tanda_terimas', function (Blueprint $table) {
            $table->dropUnique(['nomor_tanda_terima']);
            $table->dropColumn('nomor_tanda_terima');
        });
    }
};
