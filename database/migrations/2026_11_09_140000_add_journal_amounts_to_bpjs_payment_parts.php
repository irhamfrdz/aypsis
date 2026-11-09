<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran_pranota_bpjs_parts', function (Blueprint $table) {
            $table->decimal('tunjangan', 15, 2)->default(0)->after('subtotal');
            $table->decimal('biaya', 15, 2)->default(0)->after('tunjangan');
            $table->decimal('hutang', 15, 2)->default(0)->after('biaya');
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran_pranota_bpjs_parts', function (Blueprint $table) {
            $table->dropColumn(['tunjangan', 'biaya', 'hutang']);
        });
    }
};
