<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biaya_kapal_trucking', function (Blueprint $table) {
            $table->decimal('pph_percent', 5, 2)->default(2)->after('pph');
        });
    }

    public function down(): void
    {
        Schema::table('biaya_kapal_trucking', function (Blueprint $table) {
            $table->dropColumn('pph_percent');
        });
    }
};
