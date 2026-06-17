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
        Schema::table('biaya_kapal_labuh_tambat', function (Blueprint $table) {
            $table->unsignedBigInteger('bank_id')->nullable()->after('nomor_rekening');

            // Add foreign key constraint if banks table exists
            if (Schema::hasTable('banks')) {
                $table->foreign('bank_id')->references('id')->on('banks')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_kapal_labuh_tambat', function (Blueprint $table) {
            if (Schema::hasTable('banks')) {
                $table->dropForeign(['bank_id']);
            }
            $table->dropColumn('bank_id');
        });
    }
};
