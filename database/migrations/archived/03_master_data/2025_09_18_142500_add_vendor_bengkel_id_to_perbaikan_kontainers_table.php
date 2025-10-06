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
            $table->foreignId('vendor_bengkel_id')->nullable()->after('realisasi_biaya_perbaikan')->constrained('vendor_bengkel')->onDelete('set null');
            $table->string('vendor_bengkel')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perbaikan_kontainers', function (Blueprint $table) {
            $table->dropForeign(['vendor_bengkel_id']);
            $table->dropColumn('vendor_bengkel_id');
            $table->string('vendor_bengkel')->nullable(false)->change();
        });
    }
};
