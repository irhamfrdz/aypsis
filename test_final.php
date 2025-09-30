<?php<?php

include 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';require_once 'vendor/autoload.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);$app = require_once 'bootstrap/app.php';

$kernel->bootstrap();$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();



echo "Testing PembayaranPranotaPerbaikanKontainer..." . PHP_EOL;use App\Models\User;

use Illuminate\Support\Facades\Gate;

try {

    $count = App\Models\PembayaranPranotaPerbaikanKontainer::count();echo "=== TESTING GATE AFTER PROVIDER REGISTRATION ===\n";

    echo "✅ Records found: " . $count . PHP_EOL;

// Get first user

    if ($count > 0) {$user = User::first();

        $pembayaran = App\Models\PembayaranPranotaPerbaikanKontainer::first();if (!$user) {

        echo "✅ Sample record: ID=" . $pembayaran->id . ", Status=" . $pembayaran->status . PHP_EOL;    echo "❌ No users found in database\n";

        echo "✅ Tanggal Kas: " . $pembayaran->tanggal_kas . PHP_EOL;    exit;

        echo "✅ Total Pembayaran: " . $pembayaran->total_pembayaran . PHP_EOL;}

    }

echo "Testing with user: {$user->name} (ID: {$user->id})\n\n";

    echo "✅ All tests passed!" . PHP_EOL;

} catch (Exception $e) {// Test simple gate

    echo "❌ Error: " . $e->getMessage() . PHP_EOL;Gate::define('test-gate', function () {

}    return true;
});

$result = Gate::check('test-gate', $user);
echo "Simple gate result: " . ($result ? '✅ ALLOWED' : '❌ DENIED') . "\n";

// Test dashboard permission
$result2 = Gate::check('dashboard', $user);
echo "Dashboard gate result: " . ($result2 ? '✅ ALLOWED' : '❌ DENIED') . "\n";

// Test user can method
$result3 = $user->can('dashboard');
echo "User can dashboard: " . ($result3 ? '✅ ALLOWED' : '❌ DENIED') . "\n";
