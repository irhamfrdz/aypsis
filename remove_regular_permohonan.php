<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = \App\Models\User::where('username', 'admin')->first();
if (!$user) {
    echo "Admin user not found\n";
    exit(1);
}

$regularPermohonanPermissions = \App\Models\Permission::whereIn('name', [
    'permohonan',
    'permohonan.index',
    'permohonan.create',
    'permohonan.edit',
    'permohonan.delete',
    'permohonan.print',
    'permohonan.export'
])->pluck('id');

$user->permissions()->detach($regularPermohonanPermissions);

echo "Removed " . $regularPermohonanPermissions->count() . " regular permohonan permissions from admin user\n";
