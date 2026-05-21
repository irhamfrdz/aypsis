<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = [
            'master-buruh-view',
            'master-buruh-create',
            'master-buruh-update',
            'master-buruh-delete',
        ];

        $timestamp = now();
        $data = [];
        foreach ($permissions as $permission) {
            $data[] = [
                'name' => $permission,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        DB::table('permissions')->insertOrIgnore($data);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('permissions')->whereIn('name', [
            'master-buruh-view',
            'master-buruh-create',
            'master-buruh-update',
            'master-buruh-delete',
        ])->delete();
    }
};
