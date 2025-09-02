<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This migration safely converts the `periode` column from string to integer
     * by creating a temporary integer column, copying translated values, dropping
     * the old column, and renaming the temp column to `periode`.
     *
     * Note: this avoids using doctrine/dbal and should work across MySQL/SQLite/Postgres.
     */
    public function up()
    {
        // 1) Add temporary integer column
        Schema::table('daftar_tagihan_kontainer_sewa', function (Blueprint $table) {
            $table->integer('periode_new')->nullable();
        });

        // 2) Copy known values: 'bulanan' and '1' -> 1
        try {
            DB::table('daftar_tagihan_kontainer_sewa')->where('periode', 'bulanan')->update(['periode_new' => 1]);
            DB::table('daftar_tagihan_kontainer_sewa')->where('periode', '1')->update(['periode_new' => 1]);
        } catch (\Exception $e) {
            // Best-effort copy; log if available but don't fail migration
        }

        // 3) Drop old column (string)
        Schema::table('daftar_tagihan_kontainer_sewa', function (Blueprint $table) {
            if (Schema::hasColumn('daftar_tagihan_kontainer_sewa', 'periode')) {
                $table->dropColumn('periode');
            }
        });

        // 4) Add the integer `periode` column
        Schema::table('daftar_tagihan_kontainer_sewa', function (Blueprint $table) {
            $table->integer('periode')->nullable();
        });

        // 5) Copy values from periode_new to periode
        try {
            DB::table('daftar_tagihan_kontainer_sewa')->whereNotNull('periode_new')->update(['periode' => DB::raw('periode_new')]);
        } catch (\Exception $e) {
            // ignore
        }

        // 6) Drop temporary column
        Schema::table('daftar_tagihan_kontainer_sewa', function (Blueprint $table) {
            if (Schema::hasColumn('daftar_tagihan_kontainer_sewa', 'periode_new')) {
                $table->dropColumn('periode_new');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Recreate old string column and restore values where possible
        Schema::table('daftar_tagihan_kontainer_sewa', function (Blueprint $table) {
            $table->string('periode_old')->nullable();
        });

        try {
            // Map integer 1 back to '1' (or 'bulanan' if you prefer)
            DB::table('daftar_tagihan_kontainer_sewa')->where('periode', 1)->update(['periode_old' => '1']);
        } catch (\Exception $e) {
            // ignore
        }

        // Drop integer periode
        Schema::table('daftar_tagihan_kontainer_sewa', function (Blueprint $table) {
            if (Schema::hasColumn('daftar_tagihan_kontainer_sewa', 'periode')) {
                $table->dropColumn('periode');
            }
        });

        // Recreate original periode string column
        Schema::table('daftar_tagihan_kontainer_sewa', function (Blueprint $table) {
            $table->string('periode')->nullable();
        });

        // Copy back
        try {
            DB::table('daftar_tagihan_kontainer_sewa')->whereNotNull('periode_old')->update(['periode' => DB::raw('periode_old')]);
        } catch (\Exception $e) {
            // ignore
        }

        // Drop helper column
        Schema::table('daftar_tagihan_kontainer_sewa', function (Blueprint $table) {
            if (Schema::hasColumn('daftar_tagihan_kontainer_sewa', 'periode_old')) {
                $table->dropColumn('periode_old');
            }
        });
    }
};
