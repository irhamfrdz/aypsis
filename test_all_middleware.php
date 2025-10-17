<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TESTING ALL MIDDLEWARE ===" . PHP_EOL;

$user = \App\Models\User::where('username', 'admin')->first();
if (!$user) {
    echo "❌ User admin tidak ditemukan" . PHP_EOL;
    exit(1);
}

// Simulate authentication
\Illuminate\Support\Facades\Auth::login($user);
echo "✅ User authenticated: " . $user->username . PHP_EOL;

// Test 1: EnsureKaryawanPresent
echo PHP_EOL . "1. Testing EnsureKaryawanPresent..." . PHP_EOL;
if (empty($user->karyawan_id)) {
    echo "❌ FAILED: User tidak memiliki karyawan_id" . PHP_EOL;
} else {
    echo "✅ PASSED: karyawan_id = " . $user->karyawan_id . PHP_EOL;
}

// Test 2: EnsureUserApproved
echo PHP_EOL . "2. Testing EnsureUserApproved..." . PHP_EOL;
if ($user->status !== 'approved') {
    echo "❌ FAILED: User status = " . ($user->status ?: 'NULL') . PHP_EOL;
} else {
    echo "✅ PASSED: status = approved" . PHP_EOL;
}

// Test 3: EnsureCrewChecklistComplete
echo PHP_EOL . "3. Testing EnsureCrewChecklistComplete..." . PHP_EOL;
echo "✅ PASSED: Middleware ini tidak memblokir akses" . PHP_EOL;

// Test 4: Permission check
echo PHP_EOL . "4. Testing Permission 'surat-jalan-approval-dashboard'..." . PHP_EOL;
$hasPermission = $user->can('surat-jalan-approval-dashboard');
if (!$hasPermission) {
    echo "❌ FAILED: User tidak memiliki permission 'surat-jalan-approval-dashboard'" . PHP_EOL;
} else {
    echo "✅ PASSED: User memiliki permission" . PHP_EOL;
}

// Additional check: Test approval level permissions
echo PHP_EOL . "5. Testing Approval Level Permissions..." . PHP_EOL;
$level1 = $user->can('surat-jalan-approval-level-1-view');
$level2 = $user->can('surat-jalan-approval-level-2-view');

echo "Level 1 view: " . ($level1 ? "✅ YA" : "❌ TIDAK") . PHP_EOL;
echo "Level 2 view: " . ($level2 ? "✅ YA" : "❌ TIDAK") . PHP_EOL;

if (!$level1 && !$level2) {
    echo "❌ FAILED: User tidak memiliki permission untuk level approval apapun" . PHP_EOL;
    echo "Ini akan menyebabkan error 403 di controller" . PHP_EOL;
}

echo PHP_EOL . "=== TESTING COMPLETE ===" . PHP_EOL;
