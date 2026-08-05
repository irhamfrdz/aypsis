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
            $table->text('group')->nullable()->change();
            $table->text('sub_group')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('karyawan_tidak_tetaps', function (Blueprint $table) {
            $table->string('group')->nullable()->change();
            $table->string('sub_group')->nullable()->change();
        });
    }
};
