<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            'manifest-sugeng-view' => 'Melihat Manifest Sugeng',
            'manifest-sugeng-manage' => 'Mengirim dan menghapus Manifest Sugeng',
        ] as $name => $description) {
            DB::table('permissions')->insertOrIgnore([
                'name' => $name,
                'description' => $description,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('permissions')->whereIn('name', [
            'manifest-sugeng-view',
            'manifest-sugeng-manage',
        ])->delete();
    }
};
