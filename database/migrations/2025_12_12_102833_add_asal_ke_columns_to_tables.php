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
        // Check and add columns to bls table if not exist
        if (!Schema::hasColumn('bls', 'asal_kontainer')) {
            Schema::table('bls', function (Blueprint $table) {
                $table->string('asal_kontainer')->nullable()->after('nama_barang');
            });
        }
        
        if (!Schema::hasColumn('bls', 'ke')) {
            Schema::table('bls', function (Blueprint $table) {
                $table->string('ke')->nullable()->after('asal_kontainer');
            });
        }

        // Check and add columns to naik_kapal table if not exist
        if (!Schema::hasColumn('naik_kapal', 'asal_kontainer')) {
            Schema::table('naik_kapal', function (Blueprint $table) {
                $table->string('asal_kontainer')->nullable()->after('jenis_barang');
            });
        }
        
        if (!Schema::hasColumn('naik_kapal', 'ke')) {
            Schema::table('naik_kapal', function (Blueprint $table) {
                $table->string('ke')->nullable()->after('asal_kontainer');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('bls', 'asal_kontainer')) {
            Schema::table('bls', function (Blueprint $table) {
                $table->dropColumn('asal_kontainer');
            });
        }

        if (Schema::hasColumn('bls', 'ke')) {
            Schema::table('bls', function (Blueprint $table) {
                $table->dropColumn('ke');
            });
        }

        if (Schema::hasColumn('naik_kapal', 'asal_kontainer')) {
            Schema::table('naik_kapal', function (Blueprint $table) {
                $table->dropColumn('asal_kontainer');
            });
        }

        if (Schema::hasColumn('naik_kapal', 'ke')) {
            Schema::table('naik_kapal', function (Blueprint $table) {
                $table->dropColumn('ke');
            });
        }
    }
};
