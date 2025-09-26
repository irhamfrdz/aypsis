<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Admin user permissions:\n";

$user = \App\Models\User::where('role', 'admin')->first();
if($user) {
    echo 'User: ' . $user->name . PHP_EOL;
    $perms = $user->permissions()->where('name', 'like', 'pembayaran-pranota-cat%')->get();
    foreach($perms as $p) {
        echo '- ' . $p->name . PHP_EOL;
    }
} else {
    echo 'No admin user found';
}
