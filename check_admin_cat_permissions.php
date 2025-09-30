<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking admin permissions for pembayaran pranota CAT...\n";

$user = \App\Models\User::where('email', 'like', '%admin%')->first();

if (!$user) {
    echo "No admin user found!\n";
    exit;
}

echo "User: {$user->name} ({$user->email})\n";
echo "Has permission 'pembayaran-pranota-cat.create': " . ($user->hasPermission('pembayaran-pranota-cat.create') ? 'YES' : 'NO') . "\n";
echo "Has permission 'pembayaran-pranota-cat.view': " . ($user->hasPermission('pembayaran-pranota-cat.view') ? 'YES' : 'NO') . "\n";

$permissions = $user->getAllPermissions();
echo "Total permissions: " . count($permissions) . "\n";

$catPermissions = array_filter($permissions, function($p) {
    return strpos($p->name, 'pembayaran-pranota-cat') !== false;
});

echo "CAT permissions:\n";
foreach ($catPermissions as $perm) {
    echo "- {$perm->name}\n";
}
