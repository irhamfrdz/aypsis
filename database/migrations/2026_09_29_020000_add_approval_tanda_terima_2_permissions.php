<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            'approval-tanda-terima-2-view' => 'Melihat Approval Tanda Terima 2',
            'approval-tanda-terima-2-approve' => 'Mengubah shipper pada Approval Tanda Terima 2',
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
            'approval-tanda-terima-2-view',
            'approval-tanda-terima-2-approve',
        ])->delete();
    }
};
