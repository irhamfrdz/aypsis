<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckTagihanPermissions extends Command
{
    protected $signature = 'check:tagihan-permissions {username}';
    protected $description = 'Check tagihan permissions for a user';

    public function handle()
    {
        $username = $this->argument('username');

        $user = User::where('username', $username)->first();
        if (!$user) {
            $this->error("User {$username} not found!");
            return 1;
        }

        $this->info("User: {$user->name} (ID: {$user->id})");

        $hasTagihanPermission = $user->can('tagihan-kontainer-sewa-index');
        $this->info("Has 'tagihan-kontainer-sewa-index' permission: " . ($hasTagihanPermission ? 'YES' : 'NO'));

        $tagihanPerms = $user->permissions()->where('name', 'like', '%tagihan%')->get();

        $this->info("\nTagihan-related permissions:");
        if ($tagihanPerms->isEmpty()) {
            $this->warn("No tagihan permissions found!");
        } else {
            foreach ($tagihanPerms as $perm) {
                $this->line("- {$perm->name} (ID: {$perm->id})");
            }
        }

        if ($hasTagihanPermission) {
            $this->info("\n✓ User should see the menu");
        } else {
            $this->error("\n✗ User will NOT see the menu - permission issue");
        }

        return 0;
    }
}
