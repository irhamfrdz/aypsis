<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Admin User Middleware Check ===\n\n";

// Get admin user
$adminUser = DB::table('users')->where('username', 'admin')->first();
echo "Admin User ID: {$adminUser->id}\n";
echo "Admin Username: {$adminUser->username}\n";
echo "Admin Status: " . ($adminUser->is_approved ?? 'N/A') . "\n\n";

// Check 1: EnsureKaryawanPresent
echo "1. EnsureKaryawanPresent Check:\n";
if (isset($adminUser->karyawan_id) && $adminUser->karyawan_id) {
    $karyawan = DB::table('karyawan')->where('id', $adminUser->karyawan_id)->first();
    if ($karyawan) {
        echo "✓ Karyawan ID: {$adminUser->karyawan_id}\n";
        echo "✓ Karyawan Name: {$karyawan->nama}\n";
    } else {
        echo "❌ Karyawan ID {$adminUser->karyawan_id} not found in karyawan table\n";
    }
} else {
    echo "❌ No karyawan_id in user table\n";
}

// Check 2: EnsureUserApproved
echo "\n2. EnsureUserApproved Check:\n";
if (isset($adminUser->is_approved)) {
    if ($adminUser->is_approved == 1) {
        echo "✓ User is approved (is_approved = 1)\n";
    } else {
        echo "❌ User is NOT approved (is_approved = {$adminUser->is_approved})\n";
    }
} else {
    echo "⚠️  No is_approved column found\n";
}

// Check 3: EnsureCrewChecklistComplete
echo "\n3. EnsureCrewChecklistComplete Check:\n";
if (isset($adminUser->karyawan_id) && $adminUser->karyawan_id) {
    // Check crew checklist table
    $crewChecklist = DB::table('crew_checklist')->where('karyawan_id', $adminUser->karyawan_id)->first();
    if ($crewChecklist) {
        echo "✓ Crew checklist found for karyawan ID: {$adminUser->karyawan_id}\n";
        echo "  - Is Complete: " . ($crewChecklist->is_complete ?? 'N/A') . "\n";
        if (isset($crewChecklist->is_complete) && $crewChecklist->is_complete == 1) {
            echo "✓ Crew checklist is complete\n";
        } else {
            echo "❌ Crew checklist is NOT complete\n";
        }
    } else {
        echo "❌ No crew checklist found for karyawan ID: {$adminUser->karyawan_id}\n";
    }
} else {
    echo "❌ Cannot check crew checklist - no karyawan_id\n";
}

// Check if user has admin role (bypass check)
echo "\n4. Role Check (Admin bypass):\n";
$hasAdminRole = DB::table('role_user')
    ->join('roles', 'role_user.role_id', '=', 'roles.id')
    ->where('role_user.user_id', $adminUser->id)
    ->where('roles.name', 'admin')
    ->exists();

if ($hasAdminRole) {
    echo "✓ User has admin role - may bypass some middleware checks\n";
} else {
    echo "❌ User does NOT have admin role\n";
}

// Summary
echo "\n=== SUMMARY ===\n";
echo "The import access denial is likely caused by one of these middleware:\n";
echo "- EnsureKaryawanPresent: Requires valid karyawan_id\n";
echo "- EnsureUserApproved: Requires is_approved = 1\n";
echo "- EnsureCrewChecklistComplete: Requires completed crew checklist\n";
echo "\nCheck the middleware files in app/Http/Middleware/ for exact logic.\n";

?>
