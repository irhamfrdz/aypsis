<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::first();
if (!$user) {
    echo "No users found\n";
    exit(0);
}
$names = $user->permissions->pluck('name')->toArray();
echo json_encode($names, JSON_PRETTY_PRINT) . "\n";
