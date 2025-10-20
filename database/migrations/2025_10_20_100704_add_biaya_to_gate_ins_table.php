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
        Schema::table('gate_ins', function (Blueprint $table) {
            $table->enum('biaya', ['ADMINISTRASI', 'DERMAGA', 'HAULAGE', 'LOLO', 'MASA 1A', 'MASA 1B', 'MASA2', 'STEVEDORING', 'STRIPPING', 'STUFFING'])
                  ->after('kegiatan')
                  ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gate_ins', function (Blueprint $table) {
            $table->dropColumn('biaya');
        });
    }
};
