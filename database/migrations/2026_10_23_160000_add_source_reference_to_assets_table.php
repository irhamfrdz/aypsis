<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->string('source_table', 100)->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->unique(['source_table', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropUnique(['source_table', 'source_id']);
            $table->dropColumn(['source_table', 'source_id']);
        });
    }
};
