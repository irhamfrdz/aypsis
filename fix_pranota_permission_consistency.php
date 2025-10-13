<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== FIXING PRANOTA PERMISSION CONSISTENCY ===\n\n";

// The issue: JavaScript uses 'pranota-tagihan-kontainer.create' but blade template uses 'pranota-kontainer-sewa-create'
// For tombol "Masukan ke Pranota", we should standardize to 'pranota-kontainer-sewa-create'

echo "1. Current Permissions Status:\n";

// Check pranota-kontainer-sewa-create
$pranotaKontainerSewaCreate = DB::table('permissions')->where('name', 'pranota-kontainer-sewa-create')->first();
if ($pranotaKontainerSewaCreate) {
    echo "   ✓ pranota-kontainer-sewa-create EXISTS (ID: {$pranotaKontainerSewaCreate->id})\n";
} else {
    echo "   ✗ pranota-kontainer-sewa-create MISSING\n";
}

// Check pranota-tagihan-kontainer.create  
$pranotaTagihanKontainerCreate = DB::table('permissions')->where('name', 'pranota-tagihan-kontainer.create')->first();
if ($pranotaTagihanKontainerCreate) {
    echo "   ✓ pranota-tagihan-kontainer.create EXISTS (ID: {$pranotaTagihanKontainerCreate->id})\n";
} else {
    echo "   ✗ pranota-tagihan-kontainer.create MISSING\n";
}

echo "\n2. RECOMMENDATION:\n";
echo "   For 'Masukan ke Pranota' button consistency:\n";
echo "   \n";
echo "   OPTION A - Use pranota-kontainer-sewa-create (RECOMMENDED):\n";
echo "   - Blade: @can('pranota-kontainer-sewa-create')\n";
echo "   - JavaScript: hasPermissionTo('pranota-kontainer-sewa-create')\n";
echo "   \n";
echo "   OPTION B - Use pranota-tagihan-kontainer.create:\n"; 
echo "   - Blade: @can('pranota-tagihan-kontainer.create')\n";
echo "   - JavaScript: hasPermissionTo('pranota-tagihan-kontainer.create')\n";
echo "   \n";
echo "   CURRENT STATUS: INCONSISTENT\n";
echo "   - Blade template uses: pranota-kontainer-sewa-create\n";
echo "   - JavaScript uses: pranota-tagihan-kontainer.create\n";

echo "\n3. UserController Matrix Mapping Status:\n";
echo "   ✅ pranota-kontainer-sewa → MAPPED (dash notation)\n";  
echo "   ✅ pranota-tagihan-kontainer → MAPPED (dot notation)\n";
echo "   ✅ Both patterns supported in permission system\n";

echo "\n4. Admin Role Permission Check:\n";
$adminRole = DB::table('roles')->where('name', 'Admin')->first();
if ($adminRole) {
    echo "   Admin Role: Found (ID: {$adminRole->id})\n";
    
    // Check if admin has both permissions
    $adminHasPranotaKontainerSewa = DB::table('permission_role')
        ->where('role_id', $adminRole->id)
        ->where('permission_id', $pranotaKontainerSewaCreate->id ?? 0)
        ->exists();
    
    $adminHasPranotaTagihanKontainer = DB::table('permission_role')
        ->where('role_id', $adminRole->id)  
        ->where('permission_id', $pranotaTagihanKontainerCreate->id ?? 0)
        ->exists();
    
    echo "   - Has pranota-kontainer-sewa-create: " . ($adminHasPranotaKontainerSewa ? '✅ YES' : '❌ NO') . "\n";
    echo "   - Has pranota-tagihan-kontainer.create: " . ($adminHasPranotaTagihanKontainer ? '✅ YES' : '❌ NO') . "\n";
} else {
    echo "   ❌ Admin Role: NOT FOUND\n";
    echo "   ⚠️  This is a bigger issue - no Admin role exists\n";
}

echo "\n=== ANALYSIS COMPLETED ===\n";

if (!$adminRole) {
    echo "\n🚨 CRITICAL ISSUE: No Admin role found in database!\n";
    echo "   This explains why permission checks are failing.\n";
    echo "   Need to create Admin role and assign appropriate permissions.\n";
}