<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['approval-tanda-terima-pelabuhan-view', 'approval-tanda-terima-pelabuhan-approve'] as $permission) {
            DB::table('permissions')->insertOrIgnore([
                'name' => $permission,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('permissions')->whereIn('name', [
            'approval-tanda-terima-pelabuhan-view',
            'approval-tanda-terima-pelabuhan-approve',
        ])->delete();
    }
};
