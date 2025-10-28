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
        Schema::table('prospek', function (Blueprint $table) {
            $table->unsignedBigInteger('kapal_id')->nullable()->after('nama_kapal');
            $table->timestamp('tanggal_muat')->nullable()->after('kapal_id');
            
            // Add index for better performance
            $table->index('kapal_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prospek', function (Blueprint $table) {
            $table->dropIndex(['kapal_id']);
            $table->dropColumn(['kapal_id', 'tanggal_muat']);
        });
    }
};
