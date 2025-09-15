<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSyncSeeder extends Seeder
{
    /**
     * Run the database seeds to sync server database with laptop database.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting Database Synchronization...');
        $this->command->info('Syncing server database with laptop database structure');

        // Step 1: Clean old data
        $this->command->info('Step 1: Cleaning old data...');
        $this->call([
            DatabaseCleanerSeeder::class,
        ]);

        // Step 2: Sync permissions from laptop
        $this->command->info('Step 2: Syncing permissions from laptop...');
        $this->call([
            CompletePermissionSeeder::class,
        ]);

        // Step 3: Sync users
        $this->command->info('Step 3: Syncing users...');
        $this->call([
            SyncUserSeeder::class,
        ]);

        // Step 4: Sync user permissions
        $this->command->info('Step 4: Syncing user permissions...');
        $this->call([
            SyncUserPermissionSeeder::class,
        ]);

        $this->command->info('✅ Database synchronization completed successfully!');
        $this->command->info('Server database is now synced with laptop database.');
    }
}
