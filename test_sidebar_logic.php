<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TEST SIDEBAR PERMISSION LOGIC ===\n\n";

// Find user test2
$user = User::where('name', 'test2')->first();

if (!$user) {
    echo "❌ User 'test2' tidak ditemukan\n";
    exit(1);
}

echo "✅ User ditemukan: {$user->name} (ID: {$user->id})\n\n";

// Simulate sidebar logic
$isAdmin = false; // Assuming test2 is not admin
$userCanPermohonan = $user->can('permohonan');

echo "=== SIDEBAR LOGIC SIMULATION ===\n";
echo "isAdmin: " . ($isAdmin ? 'true' : 'false') . "\n";
echo "user->can('permohonan'): " . ($userCanPermohonan ? 'true' : 'false') . "\n";

$shouldShowMenu = $isAdmin || $userCanPermohonan;
echo "\nMenu Permohonan Memo should show: " . ($shouldShowMenu ? '✅ YES' : '❌ NO') . "\n";

if ($shouldShowMenu) {
    echo "\n🎉 SUCCESS: Menu Permohonan Memo akan muncul di sidebar untuk user test2!\n";
} else {
    echo "\n❌ FAILED: Menu Permohonan Memo tidak akan muncul di sidebar untuk user test2\n";
}
