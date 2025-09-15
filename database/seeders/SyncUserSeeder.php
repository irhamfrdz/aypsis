<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SyncUserSeeder extends Seeder
{
    /**
     * Sync users from laptop database to server database.
     */
    public function run(): void
    {
        $this->command->info('👥 Syncing users from laptop database...');

        // Users data from laptop database (aypsis_yang_benar.sql)
        $users = [
            [
                'id' => 1,
                'username' => 'admin',
                'password' => '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPjkuUQHqQh2O', // admin
                'status' => 'approved',
                'role_id' => 1,
                'karyawan_id' => null,
                'approved_at' => null,
                'approved_by' => null,
                'created_at' => '2025-08-17 12:00:00',
                'updated_at' => '2025-08-17 12:00:00',
            ],
            [
                'id' => 2,
                'username' => 'staff',
                'password' => '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPjkuUQHqQh2O', // staff
                'status' => 'active',
                'role_id' => null,
                'karyawan_id' => null,
                'approved_at' => null,
                'approved_by' => null,
                'created_at' => '2025-08-17 12:00:00',
                'updated_at' => '2025-08-27 23:55:23',
            ],
            [
                'id' => 3,
                'username' => 'test',
                'password' => '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPjkuUQHqQh2O', // test
                'status' => 'active',
                'role_id' => null,
                'karyawan_id' => null,
                'approved_at' => null,
                'approved_by' => null,
                'created_at' => '2025-08-17 12:00:00',
                'updated_at' => '2025-08-29 23:55:23',
            ],
            [
                'id' => 4,
                'username' => 'kiky123',
                'password' => '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPjkuUQHqQh2O', // kiky123
                'status' => 'active',
                'role_id' => 44,
                'karyawan_id' => null,
                'approved_at' => null,
                'approved_by' => null,
                'created_at' => '2025-08-17 12:00:00',
                'updated_at' => '2025-09-08 23:55:23',
            ],
            [
                'id' => 5,
                'username' => 'user5',
                'password' => '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPjkuUQHqQh2O', // user5
                'status' => 'active',
                'role_id' => null,
                'karyawan_id' => null,
                'approved_at' => null,
                'approved_by' => null,
                'created_at' => '2025-09-13 01:30:05',
                'updated_at' => '2025-09-13 01:30:05',
            ],
            [
                'id' => 10,
                'username' => 'user10',
                'password' => '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPjkuUQHqQh2O', // user10
                'status' => 'active',
                'role_id' => null,
                'karyawan_id' => null,
                'approved_at' => null,
                'approved_by' => null,
                'created_at' => '2025-09-13 01:30:05',
                'updated_at' => '2025-09-13 01:30:05',
            ],
            [
                'id' => 15,
                'username' => 'user15',
                'password' => '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPjkuUQHqQh2O', // user15
                'status' => 'active',
                'role_id' => null,
                'karyawan_id' => null,
                'approved_at' => null,
                'approved_by' => null,
                'created_at' => '2025-09-13 01:30:05',
                'updated_at' => '2025-09-13 01:30:05',
            ],
        ];

        // Clean user data - remove non-existent columns and ensure only valid columns are inserted
        $cleanUsers = [];
        foreach ($users as $user) {
            // Only include columns that actually exist in the users table
            $cleanUsers[] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'password' => $user['password'],
                'karyawan_id' => $user['karyawan_id'] ?? null,
                'status' => $user['status'] ?? 'pending',
                'approved_by' => $user['approved_by'] ?? null,
                'approved_at' => $user['approved_at'] ?? null,
                'created_at' => $user['created_at'] ?? now(),
                'updated_at' => $user['updated_at'] ?? now(),
            ];
        }

        $existingIds = DB::table('users')->pluck('id')->toArray();
        $newUsers = [];

        foreach ($cleanUsers as $user) {
            if (!in_array($user['id'], $existingIds)) {
                $newUsers[] = $user;
            }
        }

        if (!empty($newUsers)) {
            DB::table('users')->insert($newUsers);
            $this->command->info("Added " . count($newUsers) . " new users from laptop database");
        } else {
            $this->command->info("All users from laptop database already exist");
        }

        $this->command->info('✅ Users sync completed!');
    }
}
