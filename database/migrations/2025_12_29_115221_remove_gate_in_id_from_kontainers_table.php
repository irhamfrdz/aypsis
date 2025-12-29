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
            // Drop foreign key first if it exists
            $table->dropForeign(['gate_in_id']);
            
            // Then drop the column
            $table->dropColumn('gate_in_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kontainers', function (Blueprint $table) {
            $table->unsignedBigInteger('gate_in_id')->nullable();
            $table->foreign('gate_in_id')->references('id')->on('gate_ins')->onDelete('set null');
        });
    }
};
