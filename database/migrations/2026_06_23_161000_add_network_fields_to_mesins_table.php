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
        Schema::table('mesins', function (Blueprint $table) {
            $table->string('ip_address')->nullable()->after('tipe_mesin');
            $table->integer('port')->default(4370)->after('ip_address');
            $table->integer('comm_key')->default(0)->after('port');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mesins', function (Blueprint $table) {
            $table->dropColumn(['ip_address', 'port', 'comm_key']);
        });
    }
};
