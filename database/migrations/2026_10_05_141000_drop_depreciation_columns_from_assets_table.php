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
            foreach (['masa_manfaat_bulan', 'nilai_residu'] as $col) {
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
            $table->integer('masa_manfaat_bulan')->nullable()->comment('Masa manfaat dalam hitungan bulan');
            $table->decimal('nilai_residu', 15, 2)->default(0);
        });
    }
};
