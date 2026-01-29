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
            $table->foreignId('gudangs_id')->nullable()->constrained('gudangs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kontainers', function (Blueprint $table) {
            $table->dropForeign(['gudangs_id']);
            $table->dropColumn('gudangs_id');
        });
    }
};
