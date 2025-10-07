<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class CheckUserPermissions extends Command
{
    protected $signature = 'check:user-permissions {email?}';
    protected $description = 'Check user permissions for template access';

    public function handle()
    {
        $email = $this->argument('email');

        if (!$email) {
            $email = $this->ask('Masukkan email user yang ingin dicek:');
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User dengan email {$email} tidak ditemukan!");
            return 1;
        }

        $this->info("=== USER PERMISSION CHECK ===");
        $this->info("User: {$user->name} ({$user->email})");
        $this->info("Role: " . ($user->roles->pluck('name')->implode(', ') ?: 'No roles'));

        // Check specific karyawan template permission
        $permission = 'master-karyawan-template';
        $hasPermission = $user->can($permission);

        $this->info("Permission '{$permission}': " . ($hasPermission ? 'YES' : 'NO'));

        if (!$hasPermission) {
            $this->warn("User tidak memiliki permission untuk mengakses template karyawan!");

            // Check if permission exists
            $permissionExists = Permission::where('name', $permission)->exists();
            $this->info("Permission exists in database: " . ($permissionExists ? 'YES' : 'NO'));

            // Show user's permissions
            $this->info("User's permissions:");
            $userPermissions = $user->getAllPermissions();
            foreach ($userPermissions as $perm) {
                $this->line("- {$perm->name}");
            }
        }

        return 0;
    }
}
