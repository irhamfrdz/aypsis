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
        Schema::table('assets', function (Blueprint $table) {
            if (! Schema::hasColumn('assets', 'karyawan_id')) {
                $table->foreignId('karyawan_id')->nullable()->after('status')->constrained('karyawans')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            if (Schema::hasColumn('assets', 'karyawan_id')) {
                $table->dropForeign(['karyawan_id']);
                $table->dropColumn('karyawan_id');
            }
        });
    }
};
