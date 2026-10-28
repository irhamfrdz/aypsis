<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('permohonan_amprahans')
            || Schema::hasColumn('permohonan_amprahans', 'tanggal_diterima')) {
            return;
        }

        Schema::table('permohonan_amprahans', function (Blueprint $table) {
            $table->timestamp('tanggal_diterima')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        // The original create-table migration already defines this column.
        // A rollback must not remove it from databases where this repair was a no-op.
    }
};
