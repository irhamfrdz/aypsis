<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== User Table Structure ===\n\n";

try {
    // Get first user to see structure
    $user = App\Models\User::first();
    
    if ($user) {
        echo "Sample User Data:\n";
        $userData = $user->toArray();
        foreach ($userData as $field => $value) {
            echo "- {$field}: " . (is_string($value) ? $value : json_encode($value)) . "\n";
        }
    } else {
        echo "No users found in database.\n";
    }
    
    echo "\n=== All Users Status ===\n";
    $users = App\Models\User::select('id', 'username', 'status', 'is_approved')->get();
    
    foreach ($users as $user) {
        echo "ID: {$user->id} | Username: {$user->username} | Status: " . ($user->status ?? 'null') . " | Is Approved: " . ($user->is_approved ?? 'null') . "\n";
    }
    
    echo "\n=== Status Summary ===\n";
    $statusCounts = App\Models\User::groupBy('status')->selectRaw('status, count(*) as count')->get();
    foreach ($statusCounts as $status) {
        echo "Status '{$status->status}': {$status->count} users\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}