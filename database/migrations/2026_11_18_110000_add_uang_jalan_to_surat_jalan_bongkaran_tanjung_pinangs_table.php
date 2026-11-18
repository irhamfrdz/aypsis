<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('surat_jalan_bongkaran_tanjung_pinangs', 'uang_jalan')) {
            Schema::table('surat_jalan_bongkaran_tanjung_pinangs', function (Blueprint $table) {
                $table->decimal('uang_jalan', 15, 2)->default(0);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('surat_jalan_bongkaran_tanjung_pinangs', 'uang_jalan')) {
            Schema::table('surat_jalan_bongkaran_tanjung_pinangs', function (Blueprint $table) {
                $table->dropColumn('uang_jalan');
            });
        }
    }
};
