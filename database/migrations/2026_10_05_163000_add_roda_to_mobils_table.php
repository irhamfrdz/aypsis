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
        Schema::table('mobils', function (Blueprint $table) {
            if (! Schema::hasColumn('mobils', 'roda')) {
                $table->integer('roda')->nullable()->after('jenis')->comment('Jumlah roda kendaraan (e.g. 4, 6, 10, dll)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mobils', function (Blueprint $table) {
            if (Schema::hasColumn('mobils', 'roda')) {
                $table->dropColumn('roda');
            }
        });
    }
};
