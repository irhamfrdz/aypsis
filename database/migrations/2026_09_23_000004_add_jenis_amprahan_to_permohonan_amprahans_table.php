<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('permohonan_amprahans')) {
            return;
        }

        if (! Schema::hasColumn('permohonan_amprahans', 'jenis_amprahan')) {
            Schema::table('permohonan_amprahans', function (Blueprint $table) {
                $table->string('jenis_amprahan', 30)
                    ->nullable()
                    ->after('user_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('permohonan_amprahans')
            && Schema::hasColumn('permohonan_amprahans', 'jenis_amprahan')) {
            Schema::table('permohonan_amprahans', function (Blueprint $table) {
                $table->dropColumn('jenis_amprahan');
            });
        }
    }
};
