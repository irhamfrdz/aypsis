<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('karyawans', function (Blueprint $table) {
            if (!Schema::hasColumn('karyawans', 'catatan_pekerjaan')) {
                $table->text('catatan_pekerjaan')->nullable()->after('catatan')->comment('Catatan khusus untuk pekerjaan karyawan');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('karyawans', function (Blueprint $table) {
            if (Schema::hasColumn('karyawans', 'catatan_pekerjaan')) {
                $table->dropColumn('catatan_pekerjaan');
            }
        });
    }
};