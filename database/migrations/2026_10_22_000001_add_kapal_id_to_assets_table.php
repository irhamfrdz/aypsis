<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('assets', 'kapal_id')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->foreignId('kapal_id')->nullable()->after('lokasi')
                    ->constrained('master_kapals')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('assets', 'kapal_id')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->dropConstrainedForeignId('kapal_id');
            });
        }
    }
};
