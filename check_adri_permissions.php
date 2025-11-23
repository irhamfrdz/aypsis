<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking user Adri's permissions:\n";
echo "=================================\n\n";

$user = App\Models\User::where('username', 'adri')->first();

if (!$user) {
    echo "User 'adri' not found!\n";
    exit;
}

echo "User ID: {$user->id}\n";
echo "Username: {$user->username}\n";
echo "Name: {$user->name}\n\n";

echo "Tanda-Terima Permissions:\n";
echo "--------------------------\n";

$permissions = $user->permissions()->where('name', 'like', 'tanda-terima%')->get();

if ($permissions->count() === 0) {
    echo "NO PERMISSIONS FOUND!\n";
} else {
    foreach ($permissions as $permission) {
        echo "✓ {$permission->name}\n";
    }
}

echo "\n";
