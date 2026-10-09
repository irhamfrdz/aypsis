<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            $table->string('departemen')->nullable()->after('divisi');
        });
    }

    public function down(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            $table->dropColumn('departemen');
        });
    }
};
