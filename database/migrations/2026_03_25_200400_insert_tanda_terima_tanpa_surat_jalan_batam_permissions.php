<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('permissions')->insert([
            ['name' => 'tanda-terima-tanpa-surat-jalan-batam-view', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'tanda-terima-tanpa-surat-jalan-batam-create', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'tanda-terima-tanpa-surat-jalan-batam-update', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'tanda-terima-tanpa-surat-jalan-batam-delete', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'tanda-terima-tanpa-surat-jalan-batam-export', 'created_at' => now(), 'updated_at' => now()]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('permissions')->whereIn('name', [
            'tanda-terima-tanpa-surat-jalan-batam-view',
            'tanda-terima-tanpa-surat-jalan-batam-create',
            'tanda-terima-tanpa-surat-jalan-batam-update',
            'tanda-terima-tanpa-surat-jalan-batam-delete',
            'tanda-terima-tanpa-surat-jalan-batam-export'
        ])->delete();
    }
};
