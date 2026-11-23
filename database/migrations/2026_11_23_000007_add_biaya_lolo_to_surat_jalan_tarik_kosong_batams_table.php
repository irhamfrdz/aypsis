<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_jalan_tarik_kosong_batams', function (Blueprint $table): void {
            $table->decimal('biaya_lolo', 15, 2)->default(0)->after('operator_lolo_vendor');
        });
    }

    public function down(): void
    {
        Schema::table('surat_jalan_tarik_kosong_batams', function (Blueprint $table): void {
            $table->dropColumn('biaya_lolo');
        });
    }
};
