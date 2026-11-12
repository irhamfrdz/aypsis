<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('CREATE INDEX manifests_nomor_tanda_terima_approval_idx ON manifests (nomor_tanda_terima(191))');

            return;
        }

        Schema::table('manifests', function (Blueprint $table) {
            $table->index('nomor_tanda_terima', 'manifests_nomor_tanda_terima_approval_idx');
        });
    }

    public function down(): void
    {
        Schema::table('manifests', function (Blueprint $table) {
            $table->dropIndex('manifests_nomor_tanda_terima_approval_idx');
        });
    }
};
