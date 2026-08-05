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
        Schema::table('karyawan_tidak_tetaps', function (Blueprint $table) {
            $table->string('penempatan')->nullable();
            $table->string('group')->nullable();
            $table->string('sub_group')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('karyawan_tidak_tetaps', function (Blueprint $table) {
            $table->dropColumn(['penempatan', 'group', 'sub_group']);
        });
    }
};
