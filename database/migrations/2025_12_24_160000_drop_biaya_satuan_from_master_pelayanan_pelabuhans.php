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
        Schema::table('master_pelayanan_pelabuhans', function (Blueprint $table) {
            $table->dropColumn(['biaya', 'satuan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_pelayanan_pelabuhans', function (Blueprint $table) {
            $table->decimal('biaya', 15, 2)->nullable();
            $table->string('satuan')->nullable();
        });
    }
};
