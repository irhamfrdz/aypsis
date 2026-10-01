<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_pricelist_lolo_batams', function (Blueprint $table) {
            $table->dropIndex(['vendor']);
            $table->dropColumn(['vendor', 'nama_biaya']);
        });
    }

    public function down(): void
    {
        Schema::table('master_pricelist_lolo_batams', function (Blueprint $table) {
            $table->string('vendor')->nullable()->after('id');
            $table->string('nama_biaya')->nullable()->after('vendor');
            $table->index('vendor');
        });
    }
};
