<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('permohonan_amprahans')
            || Schema::hasColumn('permohonan_amprahans', 'tujuan_permintaan')) {
            return;
        }

        Schema::table('permohonan_amprahans', function (Blueprint $table) {
            $table->string('tujuan_permintaan')->nullable()->after('nomor_voyage');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('permohonan_amprahans')
            || ! Schema::hasColumn('permohonan_amprahans', 'tujuan_permintaan')) {
            return;
        }

        Schema::table('permohonan_amprahans', function (Blueprint $table) {
            $table->dropColumn('tujuan_permintaan');
        });
    }
};
