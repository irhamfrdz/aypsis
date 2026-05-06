<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('btm_sewa_permohonan_transfers', function (Blueprint $table) {
            $table->string('bank')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('btm_sewa_permohonan_transfers', function (Blueprint $table) {
            $table->string('bank')->nullable(false)->change();
        });
    }
};
