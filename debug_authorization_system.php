<?php
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

// Setup Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DEBUG AUTHORIZATION SYSTEM ===\n\n";

try {
    // Login sebagai user admin
    $user = \App\Models\User::find(1);
    Auth::login($user);

    echo "✅ Logged in as: {$user->username}\n\n";

    // Test permissions yang bermasalah
    $testPermissions = [
        'master-cabang-view',
        'master-cabang-index',
        'master-coa-view',
        'master-coa-index',
        'master-kode-nomor-view',
        'master-nomor-terakhir-view',
        'master-tipe-akun-view',
        'master-tujuan-view'
    ];

    echo "=== TEST USER->CAN() METHOD ===\n";
    foreach ($testPermissions as $perm) {
        $canResult = $user->can($perm);
        $status = $canResult ? '✅' : '❌';
        echo "$status user->can('$perm')\n";
    }

    echo "\n=== TEST GATE::ALLOWS() METHOD ===\n";
    foreach ($testPermissions as $perm) {
        $gateResult = Gate::allows($perm);
        $status = $gateResult ? '✅' : '❌';
        echo "$status Gate::allows('$perm')\n";
    }

    echo "\n=== TEST AUTH::USER()->CAN() ===\n";
    foreach ($testPermissions as $perm) {
        $authUser = Auth::user();
        $authResult = $authUser->can($perm);
        $status = $authResult ? '✅' : '❌';
        echo "$status Auth::user()->can('$perm')\n";
    }

    echo "\n=== COMPARE METHODS ===\n";
    foreach ($testPermissions as $perm) {
        $userCan = $user->can($perm);
        $gateCan = Gate::allows($perm);
        $authUser = Auth::user();
        $authCan = $authUser->can($perm);

        if ($userCan === $gateCan && $gateCan === $authCan) {
            echo "✅ $perm: ALL CONSISTENT\n";
        } else {
            echo "⚠️  $perm: INCONSISTENT - user:$userCan, gate:$gateCan, auth:$authCan\n";
        }
    }

    echo "\n=== CEK DEFINED GATES ===\n";
    // Cek apakah ada gates yang di-define secara manual
    $reflection = new ReflectionClass(Gate::class);
    $property = $reflection->getProperty('abilities');
    $property->setAccessible(true);
    $abilities = $property->getValue(Gate::getFacadeRoot());

    echo "Defined Gates: " . count($abilities) . "\n";
    if (count($abilities) > 0) {
        foreach ($abilities as $gateName => $callback) {
            if (str_contains($gateName, 'master-')) {
                echo "- $gateName\n";
            }
        }
    }

    echo "\n=== CEK MIDDLEWARE ROUTES ===\n";
    // Simulasi middleware check
    $routes = [
        'master.cabang.index' => 'master-cabang-view',
        'master-coa-index' => 'master-coa-view'
    ];

    foreach ($routes as $routeName => $permission) {
        // Simulasi cara middleware 'can' bekerja
        $middlewareResult = $user->can($permission);
        echo "$routeName -> middleware('can:$permission') = " . ($middlewareResult ? '✅ PASS' : '❌ BLOCK') . "\n";
    }

    echo "\n=== DIAGNOSIS ===\n";
    if (Auth::check()) {
        echo "✅ User is authenticated\n";
    } else {
        echo "❌ User is not authenticated\n";
    }

    $permissionCount = $user->permissions()->count();
    echo "✅ User has $permissionCount permissions\n";

    echo "\n=== SOLUSI JIKA MASIH BERMASALAH ===\n";
    echo "1. Cek apakah ada Gate::define() yang override permission\n";
    echo "2. Cek apakah ada custom middleware yang block akses\n";
    echo "3. Cek apakah ada custom AuthServiceProvider logic\n";
    echo "4. Cek session/cache mungkin masih lama\n";
    echo "5. Clear semua cache dan restart server\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
