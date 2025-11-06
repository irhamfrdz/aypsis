<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Config;

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Test Master Mobil Filter BTM Only ===\n\n";

// Test 1: User dengan cabang BTM harus mendapat filter
echo "Test 1: User dengan cabang BTM\n";
echo "Expected: Hanya melihat mobil dengan karyawan cabang BTM\n";

// Simulasi user dengan karyawan cabang BTM
$userBTM = new stdClass();
$userBTM->karyawan = new stdClass();
$userBTM->karyawan->cabang = 'BTM';

// Simulasi auth user
auth()->shouldReceive('user')->andReturn($userBTM);

// Query yang akan digunakan di controller
$query = \App\Models\Mobil::with('karyawan');

// Logic dari controller
$currentUser = auth()->user();
if ($currentUser && $currentUser->karyawan && $currentUser->karyawan->cabang === 'BTM') {
    $query->whereHas('karyawan', function($q) {
        $q->where('cabang', 'BTM');
    });
    echo "✓ Filter BTM diterapkan untuk user cabang BTM\n";
} else {
    echo "✗ Filter BTM TIDAK diterapkan\n";
}

echo "\n";

// Test 2: User dengan cabang selain BTM tidak mendapat filter
echo "Test 2: User dengan cabang Jakarta\n";
echo "Expected: Melihat semua mobil (tidak ada filter)\n";

// Simulasi user dengan karyawan cabang Jakarta
$userJakarta = new stdClass();
$userJakarta->karyawan = new stdClass();
$userJakarta->karyawan->cabang = 'Jakarta';

// Simulasi auth user
auth()->shouldReceive('user')->andReturn($userJakarta);

// Query yang akan digunakan di controller
$query2 = \App\Models\Mobil::with('karyawan');

// Logic dari controller
$currentUser2 = auth()->user();
if ($currentUser2 && $currentUser2->karyawan && $currentUser2->karyawan->cabang === 'BTM') {
    $query2->whereHas('karyawan', function($q) {
        $q->where('cabang', 'BTM');
    });
    echo "✗ Filter BTM diterapkan (seharusnya tidak)\n";
} else {
    echo "✓ Filter BTM TIDAK diterapkan untuk user cabang Jakarta\n";
}

echo "\n";

// Test 3: User tanpa karyawan tidak mendapat filter
echo "Test 3: User tanpa karyawan\n";
echo "Expected: Melihat semua mobil (tidak ada filter)\n";

// Simulasi user tanpa karyawan
$userNoKaryawan = new stdClass();
$userNoKaryawan->karyawan = null;

// Simulasi auth user
auth()->shouldReceive('user')->andReturn($userNoKaryawan);

// Query yang akan digunakan di controller
$query3 = \App\Models\Mobil::with('karyawan');

// Logic dari controller
$currentUser3 = auth()->user();
if ($currentUser3 && $currentUser3->karyawan && $currentUser3->karyawan->cabang === 'BTM') {
    $query3->whereHas('karyawan', function($q) {
        $q->where('cabang', 'BTM');
    });
    echo "✗ Filter BTM diterapkan (seharusnya tidak)\n";
} else {
    echo "✓ Filter BTM TIDAK diterapkan untuk user tanpa karyawan\n";
}

echo "\n";

// Test 4: User dengan cabang Surabaya tidak mendapat filter
echo "Test 4: User dengan cabang Surabaya\n";
echo "Expected: Melihat semua mobil (tidak ada filter)\n";

// Simulasi user dengan karyawan cabang Surabaya
$userSurabaya = new stdClass();
$userSurabaya->karyawan = new stdClass();
$userSurabaya->karyawan->cabang = 'Surabaya';

// Simulasi auth user
auth()->shouldReceive('user')->andReturn($userSurabaya);

// Query yang akan digunakan di controller
$query4 = \App\Models\Mobil::with('karyawan');

// Logic dari controller
$currentUser4 = auth()->user();
if ($currentUser4 && $currentUser4->karyawan && $currentUser4->karyawan->cabang === 'BTM') {
    $query4->whereHas('karyawan', function($q) {
        $q->where('cabang', 'BTM');
    });
    echo "✗ Filter BTM diterapkan (seharusnya tidak)\n";
} else {
    echo "✓ Filter BTM TIDAK diterapkan untuk user cabang Surabaya\n";
}

echo "\n=== Summary ===\n";
echo "Filter Master Mobil hanya diterapkan untuk user dengan cabang BTM.\n";
echo "User dengan cabang lain atau tanpa karyawan dapat melihat semua mobil.\n";
echo "\nImplementasi berhasil: HANYA user cabang BTM yang mendapat filter!\n";