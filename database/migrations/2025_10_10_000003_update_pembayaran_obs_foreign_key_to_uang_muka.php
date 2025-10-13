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
    public function up()
    {
        Schema::table('pembayaran_obs', function (Blueprint $table) {
            // Check if foreign key exists and drop it safely
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'pembayaran_obs'
                AND COLUMN_NAME = 'pembayaran_dp_ob_id'
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");

            foreach ($foreignKeys as $fk) {
                DB::statement("ALTER TABLE pembayaran_obs DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
            }
        });

        Schema::table('pembayaran_obs', function (Blueprint $table) {
            // Check if column exists before dropping
            if (Schema::hasColumn('pembayaran_obs', 'pembayaran_dp_ob_id')) {
                $table->dropColumn('pembayaran_dp_ob_id');
            }

            // Add new column with foreign key to pembayaran_uang_muka
            if (!Schema::hasColumn('pembayaran_obs', 'pembayaran_uang_muka_id')) {
                $table->foreignId('pembayaran_uang_muka_id')->nullable()->after('keterangan')->constrained('pembayaran_uang_muka')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('pembayaran_obs', function (Blueprint $table) {
            // Check if foreign key exists and drop it safely
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'pembayaran_obs'
                AND COLUMN_NAME = 'pembayaran_uang_muka_id'
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");

            foreach ($foreignKeys as $fk) {
                DB::statement("ALTER TABLE pembayaran_obs DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
            }
        });

        Schema::table('pembayaran_obs', function (Blueprint $table) {
            // Check if column exists before dropping
            if (Schema::hasColumn('pembayaran_obs', 'pembayaran_uang_muka_id')) {
                $table->dropColumn('pembayaran_uang_muka_id');
            }

            // Restore original column if not exists
            if (!Schema::hasColumn('pembayaran_obs', 'pembayaran_dp_ob_id')) {
                $table->foreignId('pembayaran_dp_ob_id')->nullable()->after('keterangan')->constrained('pembayaran_dp_obs')->onDelete('set null');
            }
        });
    }
};
