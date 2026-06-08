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
            $table->boolean('is_cat')->default(false)->after('biaya_riil');
            $table->decimal('biaya_cat', 15, 2)->default(0)->after('is_cat');
            $table->string('vendor_cat')->nullable()->after('biaya_cat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perbaikan_kontainers', function (Blueprint $table) {
            $table->dropColumn(['is_cat', 'biaya_cat', 'vendor_cat']);
        });
    }
};
