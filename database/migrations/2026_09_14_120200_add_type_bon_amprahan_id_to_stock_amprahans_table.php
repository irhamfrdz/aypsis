<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_amprahans', function (Blueprint $table) {
            $table->foreignId('type_bon_amprahan_id')
                ->nullable()
                ->after('type_amprahan')
                ->constrained('master_type_bon_amprahans')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('stock_amprahans', function (Blueprint $table) {
            $table->dropForeign(['type_bon_amprahan_id']);
            $table->dropColumn('type_bon_amprahan_id');
        });
    }
};
