<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use Illuminate\Foundation\Application;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking all users in database\n";
echo "==============================\n";

$users = User::select('id', 'username')->get();

echo "Found " . $users->count() . " users:\n";
foreach ($users as $user) {
    echo "  ID {$user->id}: {$user->username}\n";
}
