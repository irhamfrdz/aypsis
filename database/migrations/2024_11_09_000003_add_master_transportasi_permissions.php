<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $permissions = [
            [
                'name' => 'master-transportasi-view',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'master-transportasi-create',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'master-transportasi-update',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'master-transportasi-delete',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'master-transportasi-print',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'master-transportasi-export',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($permissions as $permission) {
            // Check if permission already exists
            $existing = DB::table('permissions')->where('name', $permission['name'])->first();
            
            if (!$existing) {
                DB::table('permissions')->insert($permission);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('permissions')->whereIn('name', [
            'master-transportasi-view',
            'master-transportasi-create',
            'master-transportasi-update', 
            'master-transportasi-delete',
            'master-transportasi-print',
            'master-transportasi-export'
        ])->delete();
    }
};