<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        try {
            Schema::dropIfExists('tanda_terima_surat_jalan_kontainer_sewas');
            Schema::dropIfExists('surat_jalan_kontainer_sewa_items');
            Schema::dropIfExists('surat_jalan_kontainer_sewas');

            DB::table('permissions')->whereIn('name', [
                'surat-jalan-kontainer-sewa-view',
                'surat-jalan-kontainer-sewa-create',
                'surat-jalan-kontainer-sewa-update',
                'surat-jalan-kontainer-sewa-delete',
                'surat-jalan-kontainer-sewa-print',
                'tanda-terima-surat-jalan-kontainer-sewa-view',
                'tanda-terima-surat-jalan-kontainer-sewa-create',
                'tanda-terima-surat-jalan-kontainer-sewa-update',
                'tanda-terima-surat-jalan-kontainer-sewa-delete',
            ])->delete();
        } finally {
            Schema::enableForeignKeyConstraints();
        }
    }

    public function down(): void
    {
        // These tables contain operational records and are intentionally not recreated.
    }
};
