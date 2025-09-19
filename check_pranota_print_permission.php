<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

echo "=== CHECKING PRANOTA PRINT PERMISSION ===\n\n";

// Ganti 'admin' dengan username yang ingin dicek
$username = 'admin';

$user = User::where('username', $username)->first();
if (!$user) {
    echo "User '$username' not found!\n";
    exit(1);
}

echo "User: {$user->username} (ID: {$user->id})\n\n";

echo "=== PERMISSIONS CHECK ===\n";
$permissions = $user->permissions()->where('name', 'LIKE', '%pranota-perbaikan-kontainer%')->get();
echo "Pranota permissions:\n";
foreach ($permissions as $perm) {
    echo "  - {$perm->name}\n";
}

echo "\n=== SPECIFIC PRINT PERMISSION ===\n";
$printPerm = $user->permissions()->where('name', 'pranota-perbaikan-kontainer.print')->first();
echo "Print permission: " . ($printPerm ? 'YES' : 'NO') . "\n";

echo "\n=== GATE CHECK ===\n";
// Simulate authentication
Auth::login($user);
$canPrint = Gate::allows('pranota-perbaikan-kontainer-print');
echo "Gate check: " . ($canPrint ? 'ALLOWED' : 'DENIED') . "\n";

echo "\n=== TROUBLESHOOTING ===\n";
if (!$printPerm) {
    echo "❌ Print permission not found in database\n";
    echo "   Solution: Make sure to save the permission in user edit form\n";
} else {
    echo "✅ Print permission exists in database\n";
}

if (!$canPrint) {
    echo "❌ Gate check failed\n";
    echo "   Solution: Try logout and login again to refresh permissions\n";
} else {
    echo "✅ Gate check passed\n";
}

echo "\n=== ALL USER PERMISSIONS ===\n";
$userPermissions = $user->permissions()->orderBy('name')->get();
foreach ($userPermissions as $perm) {
    echo "  - {$perm->name}\n";
}
