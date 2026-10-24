<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('type_gaji_karyawan', function (Blueprint $table) {
            $table->unsignedBigInteger('type_gaji_id')->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('type_gaji_karyawan', function (Blueprint $table) {
            $table->dropColumn('type_gaji_id');
        });
    }
};
