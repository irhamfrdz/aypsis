<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('tagihan_kontainer_sewa', 'nomor_pranota') || !Schema::hasColumn('tagihan_kontainer_sewa', 'is_pranota')) {
            Schema::table('tagihan_kontainer_sewa', function (Blueprint $table) {
                if (!Schema::hasColumn('tagihan_kontainer_sewa', 'nomor_pranota')) {
                    $table->string('nomor_pranota')->nullable()->after('nomor_kontainer');
                }
                if (!Schema::hasColumn('tagihan_kontainer_sewa', 'is_pranota')) {
                    $table->boolean('is_pranota')->default(false)->after('nomor_pranota');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('tagihan_kontainer_sewa', 'nomor_pranota') || Schema::hasColumn('tagihan_kontainer_sewa', 'is_pranota')) {
            Schema::table('tagihan_kontainer_sewa', function (Blueprint $table) {
                if (Schema::hasColumn('tagihan_kontainer_sewa', 'is_pranota')) {
                    $table->dropColumn('is_pranota');
                }
                if (Schema::hasColumn('tagihan_kontainer_sewa', 'nomor_pranota')) {
                    $table->dropColumn('nomor_pranota');
                }
            });
        }
    }
};
