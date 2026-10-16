<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            if (! Schema::hasColumn('assets', 'nomor_seri')) {
                $table->string('nomor_seri', 100)->nullable();
            }

            if (! Schema::hasColumn('assets', 'lokasi')) {
                $table->string('lokasi', 150)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $columns = [];
            foreach (['nomor_seri', 'lokasi'] as $column) {
                if (Schema::hasColumn('assets', $column)) {
                    $columns[] = $column;
                }
            }

            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
