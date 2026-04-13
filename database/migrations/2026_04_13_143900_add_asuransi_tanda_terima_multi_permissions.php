<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = [
            [
                'name' => 'asuransi-tanda-terima-multi-view',
                'description' => 'Asuransi Tanda Terima Multi View',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'asuransi-tanda-terima-multi-create',
                'description' => 'Asuransi Tanda Terima Multi Create',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'asuransi-tanda-terima-multi-update',
                'description' => 'Asuransi Tanda Terima Multi Update',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'asuransi-tanda-terima-multi-delete',
                'description' => 'Asuransi Tanda Terima Multi Delete',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        foreach ($permissions as $permission) {
            // Check if permission already exists to avoid errors on retry
            $exists = DB::table('permissions')->where('name', $permission['name'])->exists();
            if (!$exists) {
                DB::table('permissions')->insert($permission);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissions = [
            'asuransi-tanda-terima-multi-view',
            'asuransi-tanda-terima-multi-create',
            'asuransi-tanda-terima-multi-update',
            'asuransi-tanda-terima-multi-delete',
        ];

        DB::table('permissions')->whereIn('name', $permissions)->delete();
    }
};
