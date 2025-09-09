<?php
// Test timestamp casting
echo "=== TESTING TIMESTAMP CASTING ===\n";

define('LARAVEL_START', microtime(true));
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    // Get a user with approved_at field
    $user = App\Models\User::whereNotNull('approved_at')->first();
    
    if ($user) {
        echo "User: {$user->name}\n";
        echo "approved_at type: " . gettype($user->approved_at) . "\n";
        echo "approved_at class: " . get_class($user->approved_at) . "\n";
        echo "approved_at value: {$user->approved_at}\n";
        echo "formatted: " . $user->approved_at->format('d/m/Y H:i') . "\n";
        echo "diffForHumans: " . $user->approved_at->diffForHumans() . "\n";
        echo "✅ Casting working correctly!\n";
    } else {
        echo "No user with approved_at found, creating one for test...\n";
        
        $testUser = App\Models\User::first();
        $testUser->update([
            'approved_at' => now(),
            'approved_by' => 1
        ]);
        
        $testUser->refresh();
        echo "approved_at type: " . gettype($testUser->approved_at) . "\n";
        echo "approved_at class: " . get_class($testUser->approved_at) . "\n";
        echo "✅ Casting added and working!\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
