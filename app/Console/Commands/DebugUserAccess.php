<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DebugUserAccess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:user-access {username}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug user access and permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $username = $this->argument('username');
        
        $this->info("=== DEBUG USER ACCESS: {$username} ===");
        $this->newLine();

        $user = User::where('username', $username)->first();
        if (!$user) {
            $this->error("❌ User {$username} not found!");
            return 1;
        }

        $this->line("✅ User found: {$user->username}");
        $this->line("📧 Email: {$user->email}");
        $this->line("🔑 Status: {$user->status}");
        $this->line("📅 Email verified: " . ($user->email_verified_at ? 'YES' : 'NO'));
        $this->newLine();

        // Check Karyawan
        if ($user->karyawan) {
            $this->line("👤 Karyawan linked: YES (ID: {$user->karyawan->id})");
            $this->line("📋 Crew checklist complete: " . ($user->karyawan->crew_checklist_complete ? 'YES' : 'NO'));
        } else {
            $this->line("👤 Karyawan linked: NO");
        }
        $this->newLine();

        // Check permissions
        $this->info("=== PERMISSIONS ===");
        $permissions = $user->permissions;
        if ($permissions->isEmpty()) {
            $this->line("❌ User has no direct permissions");
        } else {
            foreach ($permissions as $permission) {
                $this->line("✅ {$permission->name}");
            }
        }
        $this->newLine();

        // Test specific route permissions
        $this->info("=== ROUTE PERMISSION TESTS ===");
        
        $routeTests = [
            'order-view' => 'orders index',
            'order-create' => 'orders create',
            'surat-jalan-view' => 'surat-jalan index',
            'surat-jalan-create' => 'surat-jalan create'
        ];

        foreach ($routeTests as $permission => $description) {
            $canAccess = $user->hasPermissionTo($permission);
            $icon = $canAccess ? '✅' : '❌';
            $this->line("{$icon} {$description} (can:{$permission}): " . ($canAccess ? 'YES' : 'NO'));
        }
        $this->newLine();

        // Check middleware requirements
        $this->info("=== MIDDLEWARE REQUIREMENTS ===");
        $this->line("✅ Auth: User exists (can authenticate)");
        
        // EnsureKaryawanPresent
        if ($user->karyawan_id && $user->karyawan) {
            $this->line("✅ EnsureKaryawanPresent: PASS");
        } else {
            $this->line("❌ EnsureKaryawanPresent: FAIL (no karyawan linked)");
        }

        // EnsureUserApproved
        if ($user->status === 'approved') {
            $this->line("✅ EnsureUserApproved: PASS");
        } else {
            $this->line("❌ EnsureUserApproved: FAIL (status: {$user->status})");
        }

        // Email verification
        if ($user->email_verified_at) {
            $this->line("✅ Verified: PASS");
        } else {
            $this->line("❌ Verified: FAIL (email not verified)");
        }

        // Crew checklist
        if ($user->karyawan && $user->karyawan->crew_checklist_complete) {
            $this->line("✅ EnsureCrewChecklistComplete: PASS");
        } else {
            $this->line("❌ EnsureCrewChecklistComplete: FAIL (checklist not complete)");
        }
        
        $this->newLine();
        $this->info("=== SUMMARY ===");
        $this->line("Based on current route configuration:");
        $this->line("- Orders routes: auth middleware + can:order-view permission");
        $this->line("- Surat Jalan routes: auth middleware + can:surat-jalan-view permission");
        $this->newLine();

        return 0;
    }
}
