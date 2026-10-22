<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('assets', 'nama_merk')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->string('nama_merk', 150)->nullable()->after('nama_asset');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('assets', 'nama_merk')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->dropColumn('nama_merk');
            });
        }
    }
};
