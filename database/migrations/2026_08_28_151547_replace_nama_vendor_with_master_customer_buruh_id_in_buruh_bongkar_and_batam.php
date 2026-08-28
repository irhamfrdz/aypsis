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
        Schema::table('biaya_kapal_buruh_bongkars', function (Blueprint $table) {
            $table->dropColumn('nama_vendor');
            $table->foreignId('master_customer_buruh_id')->nullable()->constrained('master_customer_buruhs')->nullOnDelete();
        });

        Schema::table('biaya_kapal_buruh_batams', function (Blueprint $table) {
            $table->dropColumn('nama_vendor');
            $table->foreignId('master_customer_buruh_id')->nullable()->constrained('master_customer_buruhs')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_kapal_buruh_bongkars', function (Blueprint $table) {
            $table->dropForeign(['master_customer_buruh_id']);
            $table->dropColumn('master_customer_buruh_id');
            $table->string('nama_vendor')->nullable();
        });

        Schema::table('biaya_kapal_buruh_batams', function (Blueprint $table) {
            $table->dropForeign(['master_customer_buruh_id']);
            $table->dropColumn('master_customer_buruh_id');
            $table->string('nama_vendor')->nullable();
        });
    }
};
