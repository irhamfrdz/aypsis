<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('tanda_terimas', 'bukti_muat')) {
            Schema::table('tanda_terimas', function (Blueprint $table) {
                $table->text('bukti_muat')->nullable()->after('gambar_checkpoint')->comment('JSON array of proof of loading files copied from Surat Jalan');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('tanda_terimas', 'bukti_muat')) {
            Schema::table('tanda_terimas', function (Blueprint $table) {
                $table->dropColumn('bukti_muat');
            });
        }
    }
};
