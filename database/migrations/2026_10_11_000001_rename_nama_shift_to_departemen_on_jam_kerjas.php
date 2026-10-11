<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('jam_kerjas')
            && Schema::hasColumn('jam_kerjas', 'nama_shift')
            && ! Schema::hasColumn('jam_kerjas', 'departemen')) {
            Schema::table('jam_kerjas', function (Blueprint $table) {
                $table->renameColumn('nama_shift', 'departemen');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('jam_kerjas')
            && Schema::hasColumn('jam_kerjas', 'departemen')
            && ! Schema::hasColumn('jam_kerjas', 'nama_shift')) {
            Schema::table('jam_kerjas', function (Blueprint $table) {
                $table->renameColumn('departemen', 'nama_shift');
            });
        }
    }
};
