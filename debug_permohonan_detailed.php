<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

$user = User::where('username', 'test4')->first();
if (!$user) {
    echo 'User test4 not found' . PHP_EOL;
    exit(1);
}

echo '=== DETAILED PERMISSION ANALYSIS ===' . PHP_EOL;
echo 'All user permissions:' . PHP_EOL;
$permissions = $user->permissions->pluck('name')->toArray();
foreach ($permissions as $perm) {
    echo "- '$perm'" . PHP_EOL;
}

echo PHP_EOL;
echo '=== CHECKING PERMOHONAN PERMISSIONS ===' . PHP_EOL;

$permohonanPermissions = array_filter($permissions, function($perm) {
    return strpos($perm, 'permohonan') === 0;
});

echo 'Permissions starting with "permohonan":' . PHP_EOL;
if (empty($permohonanPermissions)) {
    echo '- NONE FOUND' . PHP_EOL;
} else {
    foreach ($permohonanPermissions as $perm) {
        echo "- '$perm'" . PHP_EOL;
    }
}

echo PHP_EOL;
echo 'hasPermissionLike("permohonan") result: ' . ($user->hasPermissionLike('permohonan') ? '✅ TRUE' : '❌ FALSE') . PHP_EOL;

// Test the Gate manually
echo PHP_EOL;
echo '=== MANUAL GATE TEST ===' . PHP_EOL;
if ($user->hasRole('admin')) {
    echo 'User is admin: ✅ ALLOWED' . PHP_EOL;
} elseif ($user->hasPermissionLike('permohonan')) {
    echo 'hasPermissionLike check: ✅ ALLOWED' . PHP_EOL;
} else {
    echo 'All checks failed: ❌ DENIED' . PHP_EOL;
}
