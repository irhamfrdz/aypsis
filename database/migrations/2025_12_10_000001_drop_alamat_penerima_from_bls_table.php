<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('bls') && Schema::hasColumn('bls', 'alamat_penerima')) {
            Schema::table('bls', function (Blueprint $table) {
                $table->dropColumn('alamat_penerima');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('bls') && !Schema::hasColumn('bls', 'alamat_penerima')) {
            Schema::table('bls', function (Blueprint $table) {
                    $table->string('alamat_penerima')->nullable()->after('penerima');
            });
        }
    }
};
