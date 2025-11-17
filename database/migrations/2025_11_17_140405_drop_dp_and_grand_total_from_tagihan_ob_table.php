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
        Schema::table('tagihan_ob', function (Blueprint $table) {
            $table->dropColumn(['dp', 'grand_total']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihan_ob', function (Blueprint $table) {
            $table->decimal('dp', 15, 2)->default(0)->after('biaya');
            $table->decimal('grand_total', 15, 2)->default(0)->after('dp');
        });
    }
};
