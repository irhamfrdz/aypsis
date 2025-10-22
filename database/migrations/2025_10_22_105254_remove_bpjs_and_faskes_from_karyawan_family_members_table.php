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
        Schema::table('karyawan_family_members', function (Blueprint $table) {
            $table->dropColumn(['no_bpjs_kesehatan', 'faskes']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('karyawan_family_members', function (Blueprint $table) {
            $table->string('no_bpjs_kesehatan')->nullable()->after('nik_ktp');
            $table->string('faskes')->nullable()->after('no_bpjs_kesehatan');
        });
    }
};
