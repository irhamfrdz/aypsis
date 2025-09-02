<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

echo "Checking users in database...\n";
$users = User::all();
echo "Total users: " . $users->count() . "\n";

foreach($users as $user) {
    echo "ID: {$user->id}, Username: {$user->username}, Name: {$user->name}\n";
}

if($users->count() == 0) {
    echo "No users found. Creating test user...\n";

    $user = User::create([
        'name' => 'Admin Test',
        'username' => 'admin',
        'password' => bcrypt('password'),
    ]);

    echo "Created user: {$user->username} with password: password\n";
}
