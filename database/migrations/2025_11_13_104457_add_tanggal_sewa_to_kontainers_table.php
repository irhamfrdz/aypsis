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
        Schema::table('kontainers', function (Blueprint $table) {
            // Check if column doesn't exist before adding
            if (!Schema::hasColumn('kontainers', 'tanggal_mulai_sewa')) {
                $table->date('tanggal_mulai_sewa')->nullable()->after('vendor');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kontainers', function (Blueprint $table) {
            if (Schema::hasColumn('kontainers', 'tanggal_mulai_sewa')) {
                $table->dropColumn('tanggal_mulai_sewa');
            }
        });
    }
};
