<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pranota_bpjs_details', function (Blueprint $table) {
            $table->json('tunjangan_manual')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pranota_bpjs_details', function (Blueprint $table) {
            $table->dropColumn('tunjangan_manual');
        });
    }
};
