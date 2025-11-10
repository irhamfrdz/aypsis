<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== User Status Update (Direct Script) ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

// Parse arguments
$userId = null;
$username = null;
$updateAll = false;
$dryRun = false;

foreach ($argv as $arg) {
    if (strpos($arg, '--user-id=') === 0) {
        $userId = substr($arg, 10);
    } elseif (strpos($arg, '--username=') === 0) {
        $username = substr($arg, 11);
    } elseif ($arg === '--all') {
        $updateAll = true;
    } elseif ($arg === '--dry-run') {
        $dryRun = true;
    } elseif ($arg === '--help') {
        echo "Usage:\n";
        echo "  php update_users_direct.php [options]\n\n";
        echo "Options:\n";
        echo "  --user-id=ID      Update specific user by ID\n";
        echo "  --username=USER   Update specific user by username\n";
        echo "  --all             Update all non-approved users\n";
        echo "  --dry-run         Show what would be updated\n";
        echo "  --help            Show this help\n\n";
        exit(0);
    }
}

if ($dryRun) {
    echo "*** DRY RUN MODE - No changes will be made ***\n\n";
}

try {
    if ($userId) {
        $user = App\Models\User::find($userId);
        if ($user) {
            echo "Found user: {$user->username} - Current status: " . ($user->status ?? 'null') . " | Is_Approved: " . ($user->is_approved ?? 'null') . "\n";
            
            if (!$dryRun) {
                $user->status = 'approved';
                $user->is_approved = 1;
                $user->save();
                echo "✅ Updated user status to approved\n";
            } else {
                echo "🔍 DRY RUN: Would update user status to approved\n";
            }
        } else {
            echo "❌ User not found\n";
        }
        
    } elseif ($username) {
        $user = App\Models\User::where('username', $username)->first();
        if ($user) {
            echo "Found user: {$user->username} - Current status: " . ($user->status ?? 'null') . " | Is_Approved: " . ($user->is_approved ?? 'null') . "\n";
            
            if (!$dryRun) {
                $user->status = 'approved';
                $user->is_approved = 1;
                $user->save();
                echo "✅ Updated user status to approved\n";
            } else {
                echo "🔍 DRY RUN: Would update user status to approved\n";
            }
        } else {
            echo "❌ User not found\n";
        }
        
    } elseif ($updateAll) {
        $users = App\Models\User::where(function($query) {
            $query->where('status', '!=', 'approved')
                  ->orWhere('is_approved', '!=', 1);
        })->get();
        
        echo "Found " . $users->count() . " users to update:\n";
        foreach ($users as $user) {
            echo "ID: {$user->id} | Username: {$user->username} | Status: " . ($user->status ?? 'null') . " | Is_Approved: " . ($user->is_approved ?? 'null') . "\n";
        }
        
        if (!$dryRun && $users->count() > 0) {
            $updated = App\Models\User::where(function($query) {
                $query->where('status', '!=', 'approved')
                      ->orWhere('is_approved', '!=', 1);
            })->update(['status' => 'approved', 'is_approved' => 1]);
            
            echo "\n✅ Updated {$updated} users to approved status\n";
        } elseif ($users->count() > 0) {
            echo "\n🔍 DRY RUN: Would update " . $users->count() . " users to approved status\n";
        } else {
            echo "\n✅ All users are already approved\n";
        }
        
    } else {
        echo "Error: Please specify --user-id, --username, or --all\n";
        echo "Use --help for usage information\n";
        exit(1);
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\nScript completed successfully.\n";