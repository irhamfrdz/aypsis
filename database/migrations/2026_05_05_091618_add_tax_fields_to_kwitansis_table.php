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
        Schema::table('kwitansis', function (Blueprint $table) {
            $table->boolean('kena_pajak')->default(false)->after('total_invoice');
            $table->boolean('termasuk_pajak')->default(false)->after('kena_pajak');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kwitansis', function (Blueprint $table) {
            $table->dropColumn(['kena_pajak', 'termasuk_pajak']);
        });
    }
};
