<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\User;

echo "=== CHECKING USERS TABLE STRUCTURE ===\n\n";

try {
    // Check table structure
    $columns = DB::select('DESCRIBE users');
    echo "📋 Users table columns:\n";
    foreach ($columns as $column) {
        echo "  - {$column->Field} ({$column->Type})\n";
    }

    echo "\n=== CHECKING USERS ===\n";

    // Get first few users to see what columns exist
    $users = DB::table('users')->limit(3)->get();

    if ($users->count() > 0) {
        echo "\n👥 First few users:\n";
        foreach ($users as $user) {
            echo "  - ID: {$user->id}\n";
            foreach ((array) $user as $key => $value) {
                if ($key !== 'password' && $key !== 'remember_token') {
                    echo "    {$key}: " . ($value ?? 'NULL') . "\n";
                }
            }
            echo "\n";
        }
    } else {
        echo "❌ No users found in table\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "=== CHECK COMPLETE ===\n";
