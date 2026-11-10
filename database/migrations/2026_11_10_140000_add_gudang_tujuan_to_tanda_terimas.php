<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tanda_terimas', function (Blueprint $table): void {
            $table->foreignId('gudang_tujuan_id')->nullable()->after('tujuan_pengiriman')
                ->constrained('gudangs')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tanda_terimas', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('gudang_tujuan_id');
        });
    }
};
