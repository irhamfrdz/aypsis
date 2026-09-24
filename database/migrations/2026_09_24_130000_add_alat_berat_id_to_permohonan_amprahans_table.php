<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('permohonan_amprahans')
            || Schema::hasColumn('permohonan_amprahans', 'alat_berat_id')) {
            return;
        }

        Schema::table('permohonan_amprahans', function (Blueprint $table) {
            $table->unsignedBigInteger('alat_berat_id')->nullable()->after('mobil_id');
            $table->foreign('alat_berat_id')
                ->references('id')
                ->on('alat_berats')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('permohonan_amprahans')
            || ! Schema::hasColumn('permohonan_amprahans', 'alat_berat_id')) {
            return;
        }

        Schema::table('permohonan_amprahans', function (Blueprint $table) {
            $table->dropForeign(['alat_berat_id']);
            $table->dropColumn('alat_berat_id');
        });
    }
};
