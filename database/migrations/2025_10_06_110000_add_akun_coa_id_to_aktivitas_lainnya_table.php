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
        Schema::table('aktivitas_lainnya', function (Blueprint $table) {
            $table->foreignId('akun_coa_id')->nullable()->after('vendor_id')->constrained('akun_coa')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aktivitas_lainnya', function (Blueprint $table) {
            $table->dropForeign(['akun_coa_id']);
            $table->dropColumn('akun_coa_id');
        });
    }
};
