<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tagihan_lolo_batams', function (Blueprint $table) {
            if (! Schema::hasColumn('tagihan_lolo_batams', 'tipe_operator')) {
                $table->string('tipe_operator', 50)->nullable()->default('AYP')->after('vendor');
            }
            if (! Schema::hasColumn('tagihan_lolo_batams', 'operator')) {
                $table->string('operator')->nullable()->after('tipe_operator');
            }
            if (! Schema::hasColumn('tagihan_lolo_batams', 'operator_karyawan_id')) {
                $table->unsignedBigInteger('operator_karyawan_id')->nullable()->after('operator');
            }
        });

        Schema::table('tagihan_lolo_batam_items', function (Blueprint $table) {
            if (! Schema::hasColumn('tagihan_lolo_batam_items', 'tipe_operator')) {
                $table->string('tipe_operator', 50)->nullable()->after('kegiatan');
            }
            if (! Schema::hasColumn('tagihan_lolo_batam_items', 'operator')) {
                $table->string('operator')->nullable()->after('tipe_operator');
            }
            if (! Schema::hasColumn('tagihan_lolo_batam_items', 'operator_karyawan_id')) {
                $table->unsignedBigInteger('operator_karyawan_id')->nullable()->after('operator');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tagihan_lolo_batams', function (Blueprint $table) {
            $table->dropColumn(['tipe_operator', 'operator', 'operator_karyawan_id']);
        });

        Schema::table('tagihan_lolo_batam_items', function (Blueprint $table) {
            $table->dropColumn(['tipe_operator', 'operator', 'operator_karyawan_id']);
        });
    }
};
