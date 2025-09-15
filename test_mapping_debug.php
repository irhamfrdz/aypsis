<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Models\Permission;

function debugMapping($module, $action) {
    echo "\n--- Debug mapping for module={$module}, action={$action} ---\n";

    $actionMap = [
        'view' => ['index','show','view'],
        'create' => ['create','store'],
        'update' => ['edit','update'],
        'delete' => ['destroy','delete'],
        'print' => ['print'],
        'export' => ['export'],
        'import' => ['import'],
        'approve' => ['approve'],
        'access' => ['access']
    ];

    $possibleActions = isset($actionMap[$action]) ? $actionMap[$action] : [$action];

    // Master handling
    if (strpos($module, 'master-') === 0) {
        $parts = explode('-', $module);
        $base = $parts[0] ?? null;
        $sub = $parts[1] ?? null;
        echo "master detected: base={$base}, sub={$sub}\n";
        if ($base && $sub) {
            foreach ($possibleActions as $dbAction) {
                $name = $base . '.' . $sub . '.' . $dbAction;
                $found = Permission::where('name', $name)->first();
                echo "try dot full: {$name} -> " . ($found ? 'FOUND id='.$found->id : 'NOT') . "\n";
            }
            // try dot base.sub
            if (in_array($action, ['view','access'])) {
                $name = $base . '.' . $sub;
                $found = Permission::where('name', $name)->first();
                echo "try dot base.sub: {$name} -> " . ($found ? 'FOUND id='.$found->id : 'NOT') . "\n";
            }
        }
    }

    // Admin special
    if ($module === 'admin') {
        foreach ($possibleActions as $dbAction) {
            if ($dbAction === 'debug') {
                $name = 'admin.debug.perms';
            } elseif ($dbAction === 'features') {
                $name = 'admin.features';
            } else {
                $name = 'admin.' . $dbAction;
            }
            $found = Permission::where('name', $name)->first();
            echo "admin try: {$name} -> " . ($found ? 'FOUND id='.$found->id : 'NOT') . "\n";
        }
    }

    // generic dot and dash
    foreach ($possibleActions as $dbAction) {
        $a = $module . '.' . $dbAction;
        $b = $module . '-' . $dbAction;
        $c = $dbAction . '-' . $module;
        $d = $module;
        foreach ([$a,$b,$c,$d] as $name) {
            $found = Permission::where('name', $name)->first();
            echo "try {$name} -> " . ($found ? 'FOUND id='.$found->id : 'NOT') . "\n";
        }
    }
}

// Test cases
$tests = [
    ['master-karyawan','view'],
    ['dashboard','view'],
    ['master-pranota-tagihan-kontainer','access'],
    ['admin','debug'],
    ['profile','view'],
    ['user-approval','view'],
    ['tagihan-kontainer','view'],
    ['pembayaran-pranota-supir','access']
];

foreach ($tests as $t) {
    debugMapping($t[0], $t[1]);
}

echo "\nDone.\n";
