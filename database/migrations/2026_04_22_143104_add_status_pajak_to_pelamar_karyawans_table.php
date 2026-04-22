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
        Schema::table('pelamar_karyawans', function (Blueprint $table) {
            $table->string('status_pajak')->nullable()->after('status_pernikahan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelamar_karyawans', function (Blueprint $table) {
            $table->dropColumn('status_pajak');
        });
    }
};
