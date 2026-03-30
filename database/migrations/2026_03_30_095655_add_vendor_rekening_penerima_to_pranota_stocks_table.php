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
        Schema::table('pranota_stocks', function (Blueprint $table) {
            $table->string('vendor')->nullable()->after('nomor_accurate');
            $table->string('rekening')->nullable()->after('vendor');
            $table->string('penerima')->nullable()->after('rekening');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pranota_stocks', function (Blueprint $table) {
            $table->dropColumn(['vendor', 'rekening', 'penerima']);
        });
    }
};
