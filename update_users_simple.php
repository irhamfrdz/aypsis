<?php

/**
 * Simple script untuk mengubah status user menjadi approved
 * Menggunakan Laravel Artisan Command
 */

// Pastikan script dijalankan dari root directory Laravel
if (!file_exists('artisan')) {
    die("Error: Script harus dijalankan dari root directory Laravel (tempat file artisan berada)\n");
}

// Parse arguments
$userId = null;
$username = null;
$updateAll = false;
$dryRun = false;

foreach ($argv as $arg) {
    if (strpos($arg, '--user-id=') === 0) {
        $userId = substr($arg, 10);
    } elseif (strpos($arg, '--email=') === 0) {
        $username = substr($arg, 8); // Actually username, keeping --email for backward compatibility
    } elseif ($arg === '--all') {
        $updateAll = true;
    } elseif ($arg === '--dry-run') {
        $dryRun = true;
    } elseif ($arg === '--help') {
        echo "Usage:\n";
        echo "  php update_users_simple.php [options]\n\n";
        echo "Options:\n";
        echo "  --user-id=ID      Update specific user by ID\n";
        echo "  --email=USERNAME  Update specific user by username\n";
        echo "  --all             Update all non-approved users\n";
        echo "  --dry-run         Show what would be updated\n";
        echo "  --help            Show this help\n\n";
        echo "Examples:\n";
        echo "  php update_users_simple.php --user-id=123\n";
        echo "  php update_users_simple.php --email=admin\n";
        echo "  php update_users_simple.php --all --dry-run\n";
        echo "  php update_users_simple.php --all\n";
        exit(0);
    }
}

// Build the artisan command
$command = 'php artisan tinker --execute="';

if ($userId) {
    $command .= '
$user = App\Models\User::find(' . $userId . ');
if ($user) {
    echo "Found user: " . $user->username . " - Current status: " . ($user->status ?? "null") . " | Is_Approved: " . ($user->is_approved ?? "null") . "\n";';
    
    if (!$dryRun) {
        $command .= '
    $user->status = "approved";
    $user->is_approved = 1;
    $user->save();
    echo "Updated user status to approved\n";';
    } else {
        $command .= '
    echo "DRY RUN: Would update user status to approved\n";';
    }
    
    $command .= '
} else {
    echo "User not found\n";
}';

} elseif ($username) {
    $command .= '
$user = App\Models\User::where("username", "' . $username . '")->first();
if ($user) {
    echo "Found user: " . $user->username . " - Current status: " . ($user->status ?? "null") . " | Is_Approved: " . ($user->is_approved ?? "null") . "\n";';
    
    if (!$dryRun) {
        $command .= '
    $user->status = "approved";
    $user->is_approved = 1;
    $user->save();
    echo "Updated user status to approved\n";';
    } else {
        $command .= '
    echo "DRY RUN: Would update user status to approved\n";';
    }
    
    $command .= '
} else {
    echo "User not found\n";
}';

} elseif ($updateAll) {
    $command .= '
$users = App\Models\User::where("status", "!=", "approved")->orWhere("is_approved", "!=", 1)->get();
echo "Found " . $users->count() . " users to update:\n";
foreach ($users as $user) {
    echo "ID: " . $user->id . " | Username: " . $user->username . " | Status: " . ($user->status ?? "null") . " | Is_Approved: " . ($user->is_approved ?? "null") . "\n";
}';

    if (!$dryRun) {
        $command .= '
$updated = App\Models\User::where("status", "!=", "approved")->orWhere("is_approved", "!=", 1)->update(["status" => "approved", "is_approved" => 1]);
echo "Updated " . $updated . " users to approved status\n";';
    } else {
        $command .= '
echo "DRY RUN: Would update " . $users->count() . " users to approved status\n";';
    }

} else {
    echo "Error: Please specify --user-id, --email (username), or --all\n";
    echo "Use --help for usage information\n";
    exit(1);
}

$command .= '"';

echo "=== User Status Update Script ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

if ($dryRun) {
    echo "*** DRY RUN MODE - No changes will be made ***\n\n";
}

echo "Executing command...\n";
echo "Command: $command\n\n";

// Execute the command
system($command, $return_code);

if ($return_code === 0) {
    echo "\nScript completed successfully.\n";
} else {
    echo "\nScript failed with return code: $return_code\n";
}