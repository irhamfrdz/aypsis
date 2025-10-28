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
        Schema::table('bls', function (Blueprint $table) {
            $table->unsignedBigInteger('prospek_id')->nullable()->after('id');
            $table->foreign('prospek_id')->references('id')->on('prospek')->onDelete('cascade');
            $table->index('prospek_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bls', function (Blueprint $table) {
            $table->dropForeign(['prospek_id']);
            $table->dropIndex(['prospek_id']);
            $table->dropColumn('prospek_id');
        });
    }
};
