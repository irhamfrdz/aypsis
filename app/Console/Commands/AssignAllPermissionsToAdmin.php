<?php

namespace App\Console\Commands;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Console\Command;

class AssignAllPermissionsToAdmin extends Command
{
    protected $signature = 'admin:assign-all-permissions';

    protected $description = 'Assign all permissions to admin user';

    public function handle()
    {
        $this->info('🔐 MEMBERIKAN SEMUA PERMISSION UNTUK USER ADMIN...');
        $this->info('==================================================');

        try {
            // Cari user admin
            $admin = User::where('username', 'admin')->first();

            if (! $admin) {
                $this->error('❌ User admin tidak ditemukan!');

                return 1;
            }

            $this->info("👤 User admin ditemukan: {$admin->name} ({$admin->username})");

            // Get semua permission yang ada
            $allPermissions = Permission::all();
            $this->info("📋 Total permissions tersedia: {$allPermissions->count()}");

            // Hapus permission lama admin terlebih dahulu
            $admin->permissions()->detach();

            // Assign semua permission ke admin
            $permissionIds = $allPermissions->pluck('id')->toArray();
            $admin->permissions()->attach($permissionIds);

            $this->info('✅ Semua permission berhasil diberikan ke user admin!');
            $this->info('📊 Permission yang diberikan:');

            foreach ($allPermissions as $permission) {
                $this->line("   - {$permission->name}");
            }

            $this->info('');
            $this->info('🎉 SELESAI! User admin sekarang memiliki akses ke semua fitur.');

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error: {$e->getMessage()}");

            return 1;
        }
    }
}
