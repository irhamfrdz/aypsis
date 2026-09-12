<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biaya_kapal_trucking', function (Blueprint $table) {
            $table->decimal('total_biaya_20ft', 15, 2)->default(0)->after('no_bl');
            $table->decimal('total_biaya_40ft', 15, 2)->default(0)->after('total_biaya_20ft');
        });
    }

    public function down(): void
    {
        Schema::table('biaya_kapal_trucking', function (Blueprint $table) {
            $table->dropColumn(['total_biaya_20ft', 'total_biaya_40ft']);
        });
    }
};
