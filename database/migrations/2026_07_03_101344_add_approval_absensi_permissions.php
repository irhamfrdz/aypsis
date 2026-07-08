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
        $permissions = [
            'approval-absensi-view',
            'approval-absensi-approve',
        ];

        $timestamp = now();
        $permissionData = [];

        foreach ($permissions as $permission) {
            // Check if it already exists
            $exists = DB::table('permissions')->where('name', $permission)->exists();
            if (!$exists) {
                $permissionData[] = [
                    'name' => $permission,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            }
        }

        if (!empty($permissionData)) {
            DB::table('permissions')->insert($permissionData);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissions = [
            'approval-absensi-view',
            'approval-absensi-approve',
        ];

        DB::table('permissions')->whereIn('name', $permissions)->delete();
    }
};
