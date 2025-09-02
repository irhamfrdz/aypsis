<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPranotaFieldsToTagihanKontainerSewa extends Migration
{
    /**
     * Run the migrations.
     * Adds nomor_pranota (nullable string) and is_pranota (boolean) to tagihan_kontainer_sewa
     */
    public function up()
    {
        if (!Schema::hasTable('tagihan_kontainer_sewa')) {
            return;
        }

        Schema::table('tagihan_kontainer_sewa', function (Blueprint $table) {
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'nomor_pranota')) {
                $table->string('nomor_pranota')->nullable()->after('keterangan');
            }
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'is_pranota')) {
                $table->boolean('is_pranota')->default(false)->after('nomor_pranota');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (!Schema::hasTable('tagihan_kontainer_sewa')) {
            return;
        }

        Schema::table('tagihan_kontainer_sewa', function (Blueprint $table) {
            if (Schema::hasColumn('tagihan_kontainer_sewa', 'is_pranota')) {
                $table->dropColumn('is_pranota');
            }
            if (Schema::hasColumn('tagihan_kontainer_sewa', 'nomor_pranota')) {
                $table->dropColumn('nomor_pranota');
            }
        });
    }
}
