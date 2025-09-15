<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Gate;

$user = User::where('username', 'test4')->first();
if (!$user) {
    echo 'User test4 not found' . PHP_EOL;
    exit(1);
}

echo '=== DEBUGGING PERMISSION ALIASES ===' . PHP_EOL;

// Check config loading
$aliases = config('permission_aliases', []);
echo 'Permission aliases config: ' . PHP_EOL;
print_r($aliases);

echo PHP_EOL;
echo 'Checking permohonan alias: ' . (isset($aliases['permohonan']) ? '✅ EXISTS' : '❌ NOT FOUND') . PHP_EOL;

if (isset($aliases['permohonan'])) {
    echo 'Permohonan aliases: ' . implode(', ', $aliases['permohonan']) . PHP_EOL;
}

echo PHP_EOL;
echo '=== TESTING GATE LOGIC ===' . PHP_EOL;

// Test the gate logic manually
$ability = 'permohonan';
echo "Testing ability: $ability" . PHP_EOL;

if ($user->hasRole('admin')) {
    echo 'User is admin: ✅ ALLOWED' . PHP_EOL;
} elseif ($user->hasPermissionTo($ability)) {
    echo 'hasPermissionTo check: ✅ ALLOWED' . PHP_EOL;
} else {
    echo 'hasPermissionTo check: ❌ DENIED' . PHP_EOL;

    if (isset($aliases[$ability])) {
        echo 'Checking aliases: ' . implode(', ', $aliases[$ability]) . PHP_EOL;
        foreach ($aliases[$ability] as $alias) {
            echo "  Checking alias '$alias':" . PHP_EOL;
            echo "    hasPermissionTo('$alias'): " . ($user->hasPermissionTo($alias) ? '✅ YES' : '❌ NO') . PHP_EOL;
            echo "    hasPermissionLike('$alias'): " . ($user->hasPermissionLike($alias) ? '✅ YES' : '❌ NO') . PHP_EOL;
        }
    } else {
        echo 'No aliases found for this ability' . PHP_EOL;
    }
}

echo PHP_EOL;
echo 'Final Gate result: ' . (Gate::allows($ability) ? '✅ ALLOWED' : '❌ DENIED') . PHP_EOL;
