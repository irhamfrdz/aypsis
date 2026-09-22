<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('permohonan_amprahans') || Schema::hasColumn('permohonan_amprahans', 'tanggal_permohonan')) {
            return;
        }

        Schema::table('permohonan_amprahans', function (Blueprint $table) {
            $table->dateTime('tanggal_permohonan')->useCurrent()->after('user_id');
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('permohonan_amprahans') && Schema::hasColumn('permohonan_amprahans', 'tanggal_permohonan')) {
            Schema::table('permohonan_amprahans', function (Blueprint $table) {
                $table->dropColumn('tanggal_permohonan');
            });
        }
    }
};
