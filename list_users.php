<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

echo "=== LIST OF USERS ===\n\n";

$users = User::all();
echo "Total users: " . $users->count() . "\n\n";

foreach ($users as $user) {
    echo "Username: {$user->username}\n";
    echo "Email: " . ($user->email ?? 'N/A') . "\n";
    echo "Created: {$user->created_at}\n";
    echo "---\n";
}