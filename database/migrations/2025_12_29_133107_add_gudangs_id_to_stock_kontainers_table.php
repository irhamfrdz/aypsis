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
        Schema::table('stock_kontainers', function (Blueprint $table) {
            $table->unsignedBigInteger('gudangs_id')->nullable()->after('id');
            $table->foreign('gudangs_id')->references('id')->on('gudangs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_kontainers', function (Blueprint $table) {
            $table->dropForeign(['gudangs_id']);
            $table->dropColumn('gudangs_id');
        });
    }
};
