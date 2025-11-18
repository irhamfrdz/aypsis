<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Gate;

echo "=== TEST PERMISSION LANGSUNG ===\n\n";

$user = User::where('username', 'suci')->first();

if (!$user) {
    echo "✗ User suci tidak ditemukan!\n";
    exit;
}

echo "✓ User: {$user->username} (ID: {$user->id})\n\n";

// Set user sebagai authenticated user
auth()->login($user);

echo "1. TEST hasPermissionTo():\n";
$hasPerm = $user->hasPermissionTo('master-mobil-view');
echo "   hasPermissionTo('master-mobil-view'): " . ($hasPerm ? '✓ TRUE' : '✗ FALSE') . "\n\n";

echo "2. TEST can() method:\n";
$canView = $user->can('master-mobil-view');
echo "   can('master-mobil-view'): " . ($canView ? '✓ TRUE' : '✗ FALSE') . "\n\n";

echo "3. TEST Gate::allows():\n";
$gateAllows = Gate::allows('master-mobil-view');
echo "   Gate::allows('master-mobil-view'): " . ($gateAllows ? '✓ TRUE' : '✗ FALSE') . "\n\n";

echo "4. TEST Gate::denies():\n";
$gateDenies = Gate::denies('master-mobil-view');
echo "   Gate::denies('master-mobil-view'): " . ($gateDenies ? '✗ TRUE (DENIED)' : '✓ FALSE (ALLOWED)') . "\n\n";

echo "5. SEMUA PERMISSION USER:\n";
foreach ($user->getAllPermissions() as $perm) {
    echo "   - {$perm->name}\n";
}

echo "\n6. CEK ROLES:\n";
if ($user->roles->count() > 0) {
    foreach ($user->roles as $role) {
        echo "   - Role: {$role->name}\n";
        echo "     Permissions dari role:\n";
        foreach ($role->permissions as $perm) {
            echo "       - {$perm->name}\n";
        }
    }
} else {
    echo "   (Tidak punya role)\n";
}
