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
        Schema::table('perbaikan_kontainers', function (Blueprint $table) {
            $table->renameColumn('biaya_perbaikan', 'estimasi_biaya_perbaikan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perbaikan_kontainers', function (Blueprint $table) {
            $table->renameColumn('estimasi_biaya_perbaikan', 'biaya_perbaikan');
        });
    }
};
