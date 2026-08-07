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
        Schema::table('pranota_uang_makans', function (Blueprint $table) {
            $table->unsignedBigInteger('pranota_puml_id')->nullable()->after('id');
            $table->foreign('pranota_puml_id')->references('id')->on('pranota_pumls')->onDelete('set null');
        });

        Schema::table('pranota_lembur_karyawan_headers', function (Blueprint $table) {
            $table->unsignedBigInteger('pranota_puml_id')->nullable()->after('id');
            $table->foreign('pranota_puml_id')->references('id')->on('pranota_pumls')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pranota_lembur_karyawan_headers', function (Blueprint $table) {
            $table->dropForeign(['pranota_puml_id']);
            $table->dropColumn('pranota_puml_id');
        });

        Schema::table('pranota_uang_makans', function (Blueprint $table) {
            $table->dropForeign(['pranota_puml_id']);
            $table->dropColumn('pranota_puml_id');
        });
    }
};
