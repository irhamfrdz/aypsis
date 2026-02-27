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
        $tables = [
            'tanda_terimas',
            'tanda_terima_tanpa_surat_jalan',
            'tanda_terimas_lcl'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                // Change string (VARCHAR) to TEXT to support multiple paths (JSON)
                $table->text('asuransi_path')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'tanda_terimas',
            'tanda_terima_tanpa_surat_jalan',
            'tanda_terimas_lcl'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                // If using MariaDB/MySQL, downgrading from text to string might truncate data if longer than 255 chars
                // But logically we revert the type.
                $table->string('asuransi_path')->nullable()->change();
            });
        }
    }
};
