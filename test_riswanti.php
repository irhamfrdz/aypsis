<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = App\Models\User::where('username', 'riswanti')->first();
if ($user) {
    echo "User ID: " . $user->id . "\n";
    echo "Username: " . $user->username . "\n";
    echo "Has ob-view permission: " . ($user->can('ob-view') ? 'Yes' : 'No') . "\n";
    
    // Let's check what permissions they do have related to ob
    $perms = \DB::table('permissions')->where('name', 'like', '%ob%')->pluck('name');
    foreach($perms as $p) {
        if ($user->can($p)) {
            echo "Has permission: " . $p . "\n";
        }
    }
} else {
    echo "User riswanti not found\n";
}
