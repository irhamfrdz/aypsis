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
        Schema::table('master_kapals', function (Blueprint $table) {
            $table->decimal('gross_tonnage', 12, 3)->nullable()->change();
            $table->decimal('deadweight_tonnage', 12, 3)->nullable()->change();
            $table->decimal('length_overall', 12, 3)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_kapals', function (Blueprint $table) {
            $table->decimal('gross_tonnage', 12, 2)->nullable()->change();
            $table->decimal('deadweight_tonnage', 12, 2)->nullable()->change();
            $table->decimal('length_overall', 12, 2)->nullable()->change();
        });
    }
};
