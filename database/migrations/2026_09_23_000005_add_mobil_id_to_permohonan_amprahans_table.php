<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('permohonan_amprahans')
            || Schema::hasColumn('permohonan_amprahans', 'mobil_id')) {
            return;
        }

        Schema::table('permohonan_amprahans', function (Blueprint $table) {
            $table->unsignedBigInteger('mobil_id')->nullable()->after('kapal_id');
            $table->foreign('mobil_id')->references('id')->on('mobils')->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('permohonan_amprahans')
            || ! Schema::hasColumn('permohonan_amprahans', 'mobil_id')) {
            return;
        }

        Schema::table('permohonan_amprahans', function (Blueprint $table) {
            $table->dropForeign(['mobil_id']);
            $table->dropColumn('mobil_id');
        });
    }
};
