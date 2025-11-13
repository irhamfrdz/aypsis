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
        Schema::table('kontainers', function (Blueprint $table) {
            if (Schema::hasColumn('kontainers', 'tanggal_masuk_sewa')) {
                $table->dropColumn('tanggal_masuk_sewa');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kontainers', function (Blueprint $table) {
            if (!Schema::hasColumn('kontainers', 'tanggal_masuk_sewa')) {
                $table->date('tanggal_masuk_sewa')->nullable()->after('keterangan');
            }
        });
    }
};
