<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->foreignId('permohonan_amprahan_id')->nullable()
                ->unique()->constrained('permohonan_amprahans')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropUnique(['permohonan_amprahan_id']);
            $table->dropConstrainedForeignId('permohonan_amprahan_id');
        });
    }
};
