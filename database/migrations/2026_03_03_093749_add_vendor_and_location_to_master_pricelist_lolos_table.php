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
        Schema::table('master_pricelist_lolos', function (Blueprint $table) {
            $table->string('vendor')->nullable()->after('terminal');
            $table->string('lokasi')->nullable()->after('vendor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_pricelist_lolos', function (Blueprint $table) {
            $table->dropColumn(['vendor', 'lokasi']);
        });
    }
};
