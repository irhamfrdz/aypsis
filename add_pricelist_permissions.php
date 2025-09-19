<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = \App\Models\User::where('username', 'admin')->first();
if (!$user) {
    echo "Admin user not found\n";
    exit(1);
}

$pricelistPermissions = \App\Models\Permission::whereIn('name', [
    'master-pricelist-sewa-kontainer',
    'master-pricelist-sewa-kontainer-view',
    'master-pricelist-sewa-kontainer-create',
    'master-pricelist-sewa-kontainer-update',
    'master-pricelist-sewa-kontainer-delete'
])->get();

$user->permissions()->syncWithoutDetaching($pricelistPermissions->pluck('id'));

echo "Added " . $pricelistPermissions->count() . " pricelist permissions to admin user\n";
echo "Permissions added: " . implode(', ', $pricelistPermissions->pluck('name')->toArray()) . "\n";