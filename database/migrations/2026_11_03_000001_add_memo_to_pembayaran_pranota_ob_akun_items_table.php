<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('pembayaran_pranota_ob_akun_items', 'memo')) {
            Schema::table('pembayaran_pranota_ob_akun_items', function (Blueprint $table) {
                $table->text('memo')->nullable()->after('nominal');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pembayaran_pranota_ob_akun_items', 'memo')) {
            Schema::table('pembayaran_pranota_ob_akun_items', function (Blueprint $table) {
                $table->dropColumn('memo');
            });
        }
    }
};
