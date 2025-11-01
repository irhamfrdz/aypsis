<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VerifyPermissionsSeeder extends Seeder
{
    /**
     * Verify permissions in database
     */
    public function run(): void
    {
        $totalPermissions = DB::table('permissions')->count();
        $this->command->info("🔢 Total permissions in database: {$totalPermissions}");
        
        $kenekPermissions = DB::table('permissions')
            ->where('name', 'like', '%kenek%')
            ->get(['name', 'description']);
        
        $this->command->info("🎯 Pranota Uang Kenek permissions ({$kenekPermissions->count()}):");
        foreach($kenekPermissions as $perm) {
            $this->command->info("   ✅ {$perm->name}: {$perm->description}");
        }
        
        $allPranotaUangPermissions = DB::table('permissions')
            ->where('name', 'like', 'pranota-uang-%')
            ->get(['name', 'description']);
        
        $this->command->info("💰 All Pranota Uang permissions ({$allPranotaUangPermissions->count()}):");
        foreach($allPranotaUangPermissions as $perm) {
            $this->command->info("   ✅ {$perm->name}: {$perm->description}");
        }
        
        $this->command->info("🎉 Verification completed successfully!");
    }
}
