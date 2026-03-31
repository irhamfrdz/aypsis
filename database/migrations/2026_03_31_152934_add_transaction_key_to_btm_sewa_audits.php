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
        Schema::table('btm_sewa_audits', function (Blueprint $table) {
            $table->string('transaction_key')->nullable()->after('transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('btm_sewa_audits', function (Blueprint $table) {
            $table->dropColumn('transaction_key');
        });
    }
};
