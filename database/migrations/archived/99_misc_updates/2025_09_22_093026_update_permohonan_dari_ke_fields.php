<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('permohonans', function (Blueprint $table) {
            // Tambahkan kolom dari dan ke
            $table->string('dari')->nullable()->after('ukuran');
            $table->string('ke')->nullable()->after('dari');
        });

        // Migrate data dari kolom tujuan ke kolom dari dan ke
        DB::statement("
            UPDATE permohonans
            SET dari = SUBSTRING_INDEX(tujuan, ' - ', 1),
                ke = SUBSTRING_INDEX(tujuan, ' - ', -1)
            WHERE tujuan IS NOT NULL AND tujuan != ''
        ");

        Schema::table('permohonans', function (Blueprint $table) {
            // Hapus kolom tujuan
            $table->dropColumn('tujuan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permohonans', function (Blueprint $table) {
            // Tambahkan kembali kolom tujuan
            $table->string('tujuan')->nullable()->after('ukuran');
        });

        // Migrate data kembali ke kolom tujuan
        DB::statement("
            UPDATE permohonans
            SET tujuan = CONCAT(COALESCE(dari, ''), ' - ', COALESCE(ke, ''))
            WHERE dari IS NOT NULL OR ke IS NOT NULL
        ");

        Schema::table('permohonans', function (Blueprint $table) {
            // Hapus kolom dari dan ke
            $table->dropColumn(['dari', 'ke']);
        });
    }
};
