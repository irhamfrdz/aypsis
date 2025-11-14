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
        Schema::table('naik_kapal', function (Blueprint $table) {
            $table->boolean('sudah_ob')->default(false)->after('kuantitas')->comment('Status sudah OB atau belum');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('naik_kapal', function (Blueprint $table) {
            $table->dropColumn('sudah_ob');
        });
    }
};
