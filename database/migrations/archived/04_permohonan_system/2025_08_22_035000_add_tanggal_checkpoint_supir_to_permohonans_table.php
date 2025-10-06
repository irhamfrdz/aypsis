<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('permohonans', 'tanggal_checkpoint_supir')) {
            Schema::table('permohonans', function (Blueprint $table) {
                $table->date('tanggal_checkpoint_supir')->nullable()->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('permohonans', 'tanggal_checkpoint_supir')) {
            Schema::table('permohonans', function (Blueprint $table) {
                $table->dropColumn('tanggal_checkpoint_supir');
            });
        }
    }
};
