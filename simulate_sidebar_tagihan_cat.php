<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use App\Models\User;
use Illuminate\Support\Facades\Auth;

$user = User::where('username', 'admin')->first();
if ($user) {
    Auth::login($user);
    echo '=== SIDEBAR SIMULATION FOR ADMIN USER (TAGIHAN CAT) ===' . PHP_EOL;
    echo 'User: ' . $user->username . PHP_EOL;
    echo PHP_EOL;

    // Check if user has aktivitas kontainer permissions
    $hasAktivitasKontainerPermissions = $user && (
        $user->can('tagihan-kontainer-sewa-index') ||
        $user->can('pranota.view') ||
        $user->can('perbaikan-kontainer-view') ||
        $user->can('pranota-perbaikan-kontainer-view') ||
        $user->can('tagihan-cat-view') ||
        $user->can('pranota-cat-view')
    );

    echo 'Has Aktivitas Kontainer Permissions: ' . ($hasAktivitasKontainerPermissions ? 'YES' : 'NO') . PHP_EOL;
    echo PHP_EOL;

    // Check specific tagihan-cat permissions
    echo 'Tagihan CAT specific checks:' . PHP_EOL;
    echo '  - tagihan-cat-view: ' . ($user->can('tagihan-cat-view') ? 'YES' : 'NO') . PHP_EOL;
    echo '  - tagihan-cat-index: ' . ($user->can('tagihan-cat-index') ? 'YES' : 'NO') . PHP_EOL;
    echo PHP_EOL;

    echo 'CONCLUSION:' . PHP_EOL;
    if ($hasAktivitasKontainerPermissions) {
        echo '✅ Aktivitas Kontainer section SHOULD appear in sidebar' . PHP_EOL;
        if ($user->can('tagihan-cat-view')) {
            echo '✅ Tagihan CAT menu SHOULD be visible' . PHP_EOL;
        } else {
            echo '❌ Tagihan CAT menu will NOT be visible (missing tagihan-cat-view)' . PHP_EOL;
        }
    } else {
        echo '❌ Aktivitas Kontainer section will NOT appear' . PHP_EOL;
    }

} else {
    echo 'Admin user not found' . PHP_EOL;
}
