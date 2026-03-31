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
        Schema::table('asuransi_tanda_terimas', function (Blueprint $table) {
            $table->decimal('asuransi_rate', 8, 4)->after('nilai_pertanggungan')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asuransi_tanda_terimas', function (Blueprint $table) {
            $table->dropColumn('asuransi_rate');
        });
    }
};
