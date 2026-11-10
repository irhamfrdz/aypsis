<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tanda_terimas', function (Blueprint $table): void {
            $table->date('tanggal_gudang_tujuan')->nullable()->after('gudang_tujuan_id');
        });
    }

    public function down(): void
    {
        Schema::table('tanda_terimas', function (Blueprint $table): void {
            $table->dropColumn('tanggal_gudang_tujuan');
        });
    }
};
