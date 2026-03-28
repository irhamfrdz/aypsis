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
            'kontainer-sewa-final-view',
            'kontainer-sewa-final-create',
            'kontainer-sewa-final-update',
            'kontainer-sewa-final-delete',
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

    public function down(): void
    {
        DB::table('permissions')->whereIn('name', [
            'kontainer-sewa-final-view',
            'kontainer-sewa-final-create',
            'kontainer-sewa-final-update',
            'kontainer-sewa-final-delete',
        ])->delete();
    }
};
