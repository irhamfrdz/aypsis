<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = App\Models\User::where('username', 'kiky')->first();
if ($user) {
    echo "User ID: " . $user->id . "\n";
    echo "Username: " . $user->username . "\n";
    echo "Has bl-view permission: " . ($user->can('bl-view') ? 'Yes' : 'No') . "\n";
    
    // Check all BL permissions
    $perms = \DB::table('permissions')->where('name', 'like', '%bl%')->pluck('name');
    foreach($perms as $p) {
        if ($user->can($p)) {
            echo "Has permission: " . $p . "\n";
        }
    }
} else {
    echo "User kiky not found\n";
}
