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
        Schema::table('assets', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach (['merk', 'tipe_model', 'nomor_seri'] as $col) {
                if (Schema::hasColumn('assets', $col)) {
                    $columnsToDrop[] = $col;
                }
            }

            if (! empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->string('merk', 100)->nullable();
            $table->string('tipe_model', 100)->nullable();
            $table->string('nomor_seri', 100)->nullable()->index();
        });
    }
};
