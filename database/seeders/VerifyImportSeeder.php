<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VerifyImportSeeder extends Seeder
{
    /**
     * Verify database import results
     */
    public function run(): void
    {
        $this->command->info("🔍 Verifying database import results...");

        // Check some key tables
        $tablesToCheck = [
            'karyawans' => 'Data Karyawan',
            'divisis' => 'Data Divisi', 
            'akun_coa' => 'Chart of Accounts',
            'master_tujuans' => 'Master Tujuan',
            'kontainers' => 'Master Kontainer',
            'surat_jalans' => 'Surat Jalan',
            'orders' => 'Orders',
            'prenomors' => 'Prenomor System',
            'pranota_uang_keneks' => 'Pranota Uang Kenek'
        ];

        $this->command->info("📊 Table Summary:");
        foreach ($tablesToCheck as $table => $description) {
            try {
                $count = DB::table($table)->count();
                $this->command->info("   ✅ {$description}: {$count} records");
            } catch (\Exception $e) {
                $this->command->warn("   ⚠️  {$description}: Table not found or error");
            }
        }

        // Check users and permissions
        $userCount = DB::table('users')->count();
        $permissionCount = DB::table('permissions')->count();
        $userPermissionCount = DB::table('user_permissions')->count();

        $this->command->info("🔐 Security System:");
        $this->command->info("   - Users: {$userCount}");
        $this->command->info("   - Permissions: {$permissionCount}"); 
        $this->command->info("   - User-Permission Relations: {$userPermissionCount}");

        // Check admin user
        $adminUser = DB::table('users')->where('username', 'admin')->first();
        if ($adminUser) {
            $adminPermissions = DB::table('user_permissions')
                ->where('user_id', $adminUser->id)
                ->count();
            $this->command->info("   ✅ Admin user has {$adminPermissions} permissions");
        }

        // Check pranota uang kenek system
        $pranotaCount = DB::table('pranota_uang_keneks')->count();
        $pranotaDetailCount = DB::table('pranota_uang_kenek_details')->count();
        $this->command->info("💰 Pranota Uang Kenek System:");
        $this->command->info("   - Pranota Records: {$pranotaCount}");
        $this->command->info("   - Detail Records: {$pranotaDetailCount}");

        $this->command->info("🎉 Database import verification completed!");
    }
}
