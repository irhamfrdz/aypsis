<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Admin User Status Check ===\n";

$user = DB::table('users')->where('username', 'admin')->first();
echo "Admin status: " . ($user->status ?? 'NULL') . "\n";
echo "Expected status: 'approved'\n";

if ($user->status === 'approved') {
    echo "✓ Status is correct\n";
} else {
    echo "❌ Status is NOT 'approved' - this will block access\n";
    echo "Fix: UPDATE users SET status = 'approved' WHERE username = 'admin'\n";
}

?>
