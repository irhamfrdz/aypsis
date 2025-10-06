<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Permission;

class PranotaKontainerSewaEditPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds to add pranota-kontainer-sewa-edit permission.
     *
     * This seeder ensures that the edit permission for pranota kontainer sewa exists
     * and is assigned to admin users.
     */
    public function run(): void
    {
        $this->command->info('🔧 Adding pranota-kontainer-sewa-edit permission...');

        // Check if permission already exists
        $permission = Permission::where('name', 'pranota-kontainer-sewa-edit')->first();

        if (!$permission) {
            // Create the permission
            $permission = Permission::create([
                'name' => 'pranota-kontainer-sewa-edit',
                'description' => 'Edit Pranota Kontainer Sewa',
            ]);

            $this->command->info('✅ Created permission: pranota-kontainer-sewa-edit');
        } else {
            $this->command->info('⏭️  Permission already exists: pranota-kontainer-sewa-edit');
        }

        // Assign permission to admin users
        $this->command->info('👤 Assigning permission to admin users...');

        // Get admin users (users with admin role or username 'admin')
        $adminUsers = User::where(function($query) {
            $query->where('username', 'admin')
                  ->orWhereHas('roles', function($q) {
                      $q->where('name', 'admin');
                  });
        })->get();

        $assignedCount = 0;
        foreach ($adminUsers as $user) {
            if (!$user->hasPermissionTo('pranota-kontainer-sewa-edit')) {
                $user->givePermissionTo('pranota-kontainer-sewa-edit');
                $assignedCount++;
                $this->command->info("✅ Assigned to user: {$user->username} ({$user->name})");
            } else {
                $this->command->info("⏭️  User already has permission: {$user->username} ({$user->name})");
            }
        }

        $this->command->info("🎉 Seeding completed! Permission created/verified and assigned to {$assignedCount} admin users.");
    }
}