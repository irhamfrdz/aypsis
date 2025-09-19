<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = \App\Models\User::where('username', 'admin')->first();
if (!$user) {
    echo "Admin user not found\n";
    exit(1);
}

$permohonanPermissions = \App\Models\Permission::whereIn('name', [
    'permohonan',
    'permohonan.index',
    'permohonan.create',
    'permohonan.edit',
    'permohonan.delete',
    'permohonan.print',
    'permohonan.export'
])->get();

$user->permissions()->syncWithoutDetaching($permohonanPermissions->pluck('id'));

echo "Added " . $permohonanPermissions->count() . " permohonan permissions to admin user\n";
echo "Permissions added: " . implode(', ', $permohonanPermissions->pluck('name')->toArray()) . "\n";
