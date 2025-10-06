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
        // Get current table structure
        $columns = Schema::getColumnListing('tipe_akuns');

        Schema::table('tipe_akuns', function (Blueprint $table) use ($columns) {
            // Handle old columns that might exist
            if (in_array('kode_tipe', $columns)) {
                $table->dropColumn('kode_tipe');
            }
            if (in_array('nama_tipe', $columns)) {
                $table->dropColumn('nama_tipe');
            }
            if (in_array('keterangan', $columns)) {
                $table->dropColumn('keterangan');
            }

            // Add correct columns if they don't exist
            if (!in_array('tipe_akun', $columns)) {
                $table->string('tipe_akun')->nullable()->after('id');
            }
            if (!in_array('catatan', $columns)) {
                $table->text('catatan')->nullable()->after('tipe_akun');
            }

            // Ensure timestamps exist
            if (!in_array('created_at', $columns)) {
                $table->timestamps();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Get current table structure
        $columns = Schema::getColumnListing('tipe_akuns');

        Schema::table('tipe_akuns', function (Blueprint $table) use ($columns) {
            // Drop new columns if they exist
            if (in_array('tipe_akun', $columns)) {
                $table->dropColumn('tipe_akun');
            }
            if (in_array('catatan', $columns)) {
                $table->dropColumn('catatan');
            }

            // Restore old columns if they don't exist
            if (!in_array('kode_tipe', $columns)) {
                $table->string('kode_tipe')->nullable()->after('id');
            }
            if (!in_array('nama_tipe', $columns)) {
                $table->string('nama_tipe')->nullable()->after('kode_tipe');
            }
            if (!in_array('keterangan', $columns)) {
                $table->text('keterangan')->nullable()->after('nama_tipe');
            }
        });
    }
};
