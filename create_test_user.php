<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Creating Test User for Update Script Demo ===\n\n";

try {
    // Create a test user that needs approval
    $testUser = new App\Models\User();
    $testUser->username = 'test_user_' . time();
    $testUser->password = bcrypt('password123'); // Add required password
    $testUser->status = 'pending';
    $testUser->is_approved = 0;
    $testUser->role = 'user';
    $testUser->karyawan_id = null; // Set to null to avoid foreign key constraint
    $testUser->save();
    
    echo "✅ Created test user:\n";
    echo "ID: {$testUser->id}\n";
    echo "Username: {$testUser->username}\n";
    echo "Status: {$testUser->status}\n";
    echo "Is Approved: {$testUser->is_approved}\n\n";
    
    echo "Now you can test the update script with:\n";
    echo "php update_users_simple.php --user-id={$testUser->id} --dry-run\n";
    echo "php update_users_simple.php --user-id={$testUser->id}\n";
    echo "php update_users_simple.php --all --dry-run\n";
    echo "php update_users_simple.php --all\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}