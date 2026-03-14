<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('master_pricelist_lolos', 'nama_biaya')) {
            Schema::table('master_pricelist_lolos', function (Blueprint $table) {
                $table->string('nama_biaya')->nullable()->after('vendor');
            });
        }
    }

    public function down(): void
    {
        Schema::table('master_pricelist_lolos', function (Blueprint $table) {
            $table->dropColumn('nama_biaya');
        });
    }
};
