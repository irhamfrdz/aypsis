<?php

/**
 * Script untuk mengubah status user menjadi approved
 * 
 * Usage:
 * php update_user_status_approved.php
 * atau
 * php update_user_status_approved.php --user-id=123
 * atau
 * php update_user_status_approved.php --email=user@example.com
 */

require_once __DIR__ . '/vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use Illuminate\Database\Capsule\Manager as DB;

// Setup database connection
$capsule = new DB;
$capsule->addConnection([
    'driver'    => env('DB_CONNECTION', 'mysql'),
    'host'      => env('DB_HOST', 'localhost'),
    'database'  => env('DB_DATABASE'),
    'username'  => env('DB_USERNAME'),
    'password'  => env('DB_PASSWORD'),
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix'    => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

function showHelp() {
    echo "Usage:\n";
    echo "  php update_user_status_approved.php [options]\n\n";
    echo "Options:\n";
    echo "  --user-id=ID        Update specific user by ID\n";
    echo "  --email=EMAIL       Update specific user by email\n";
    echo "  --all               Update all pending users to approved\n";
    echo "  --dry-run          Show what would be updated without making changes\n";
    echo "  --help             Show this help message\n\n";
    echo "Examples:\n";
    echo "  php update_user_status_approved.php --user-id=123\n";
    echo "  php update_user_status_approved.php --email=john@example.com\n";
    echo "  php update_user_status_approved.php --all\n";
    echo "  php update_user_status_approved.php --all --dry-run\n";
}

function updateUserStatus($userId = null, $email = null, $updateAll = false, $dryRun = false) {
    try {
        $query = DB::table('users');
        
        if ($userId) {
            $query->where('id', $userId);
            echo "Searching for user with ID: {$userId}\n";
        } elseif ($email) {
            $query->where('email', $email);
            echo "Searching for user with email: {$email}\n";
        } elseif ($updateAll) {
            $query->where('status', '!=', 'approved');
            echo "Searching for all non-approved users\n";
        } else {
            echo "Error: Please specify --user-id, --email, or --all\n";
            return false;
        }
        
        $users = $query->get();
        
        if ($users->isEmpty()) {
            echo "No users found matching the criteria.\n";
            return true;
        }
        
        echo "Found " . count($users) . " user(s):\n";
        echo str_repeat("-", 80) . "\n";
        
        foreach ($users as $user) {
            echo sprintf("ID: %d | Email: %s | Name: %s | Current Status: %s\n", 
                $user->id, 
                $user->email, 
                $user->name ?? 'N/A',
                $user->status ?? 'NULL'
            );
        }
        
        echo str_repeat("-", 80) . "\n";
        
        if ($dryRun) {
            echo "DRY RUN: No changes made. Above users would be updated to 'approved' status.\n";
            return true;
        }
        
        // Ask for confirmation unless it's a single user update
        if ($updateAll && count($users) > 1) {
            echo "Do you want to update all these users to 'approved' status? (yes/no): ";
            $handle = fopen("php://stdin", "r");
            $confirmation = trim(fgets($handle));
            fclose($handle);
            
            if (strtolower($confirmation) !== 'yes' && strtolower($confirmation) !== 'y') {
                echo "Operation cancelled.\n";
                return true;
            }
        }
        
        // Perform the update
        $userIds = $users->pluck('id')->toArray();
        
        $updated = DB::table('users')
            ->whereIn('id', $userIds)
            ->update([
                'status' => 'approved',
                'updated_at' => now()
            ]);
        
        echo "\nSuccessfully updated {$updated} user(s) to 'approved' status.\n";
        
        // Show updated users
        echo "\nUpdated users:\n";
        echo str_repeat("-", 80) . "\n";
        
        $updatedUsers = DB::table('users')->whereIn('id', $userIds)->get();
        foreach ($updatedUsers as $user) {
            echo sprintf("ID: %d | Email: %s | Name: %s | Status: %s\n", 
                $user->id, 
                $user->email, 
                $user->name ?? 'N/A',
                $user->status
            );
        }
        
        return true;
        
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        return false;
    }
}

// Parse command line arguments
$options = getopt('', [
    'user-id:',
    'email:',
    'all',
    'dry-run',
    'help'
]);

if (isset($options['help'])) {
    showHelp();
    exit(0);
}

$userId = $options['user-id'] ?? null;
$email = $options['email'] ?? null;
$updateAll = isset($options['all']);
$dryRun = isset($options['dry-run']);

// Validate arguments
$argCount = ($userId ? 1 : 0) + ($email ? 1 : 0) + ($updateAll ? 1 : 0);

if ($argCount === 0) {
    echo "Error: Please specify one of --user-id, --email, or --all\n\n";
    showHelp();
    exit(1);
}

if ($argCount > 1) {
    echo "Error: Please specify only one of --user-id, --email, or --all\n\n";
    showHelp();
    exit(1);
}

echo "=== User Status Update Script ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

if ($dryRun) {
    echo "*** DRY RUN MODE - No changes will be made ***\n\n";
}

$success = updateUserStatus($userId, $email, $updateAll, $dryRun);

if ($success) {
    echo "\nScript completed successfully.\n";
    exit(0);
} else {
    echo "\nScript failed.\n";
    exit(1);
}