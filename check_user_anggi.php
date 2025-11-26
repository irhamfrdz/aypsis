<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CHECKING USER ANGGI ===\n\n";

// 1. Cek user anggi
$user = App\Models\User::where('username', 'anggi')->first();

if (!$user) {
    echo "❌ User 'anggi' tidak ditemukan!\n";
    
    // Tampilkan semua user yang ada
    echo "\n📋 Daftar semua users:\n";
    $users = App\Models\User::select('id', 'username', 'name', 'email', 'status', 'role')->get();
    foreach ($users as $u) {
        echo "  - ID: {$u->id}, Username: {$u->username}, Name: {$u->name}, Status: {$u->status}, Role: {$u->role}\n";
    }
    exit;
}

echo "✅ User ditemukan!\n";
echo "   Username: {$user->username}\n";
echo "   Name: {$user->name}\n";
echo "   Email: {$user->email}\n";
echo "   Status: {$user->status}\n";
echo "   Role: {$user->role}\n";
echo "   Created: {$user->created_at}\n\n";

// 2. Cek karyawan terkait
echo "=== CHECKING KARYAWAN DATA ===\n";
$karyawan = App\Models\Karyawan::where('user_id', $user->id)->first();

if (!$karyawan) {
    echo "❌ Data karyawan tidak ditemukan untuk user anggi!\n";
} else {
    echo "✅ Data karyawan ditemukan!\n";
    echo "   ID: {$karyawan->id}\n";
    echo "   Nama Panggilan: {$karyawan->nama_panggilan}\n";
    echo "   Nama Lengkap: {$karyawan->nama_lengkap}\n";
    echo "   NIK: {$karyawan->nik}\n";
    echo "   Status: " . ($karyawan->status ?: 'null') . "\n";
    echo "   Divisi: {$karyawan->divisi}\n";
    echo "   Pekerjaan: {$karyawan->pekerjaan}\n\n";
}

// 3. Cek permissions
echo "=== CHECKING PERMISSIONS ===\n";
$permissions = $user->permissions;

if ($permissions->count() == 0) {
    echo "❌ User tidak memiliki permission apapun!\n";
} else {
    echo "✅ User memiliki {$permissions->count()} permissions:\n";
    foreach ($permissions as $perm) {
        echo "   - {$perm->name}\n";
    }
}

// 4. Cek permission order khusus
echo "\n=== CHECKING ORDER PERMISSIONS ===\n";
$orderPermissions = $user->permissions->filter(function($perm) {
    return strpos($perm->name, 'order') !== false;
});

if ($orderPermissions->count() == 0) {
    echo "❌ User tidak memiliki permission untuk order!\n";
} else {
    echo "✅ User memiliki {$orderPermissions->count()} order permissions:\n";
    foreach ($orderPermissions as $perm) {
        echo "   - {$perm->name}\n";
    }
}

// 5. Cek apakah user bisa akses order berdasarkan gate
echo "\n=== CHECKING ORDER ACCESS GATES ===\n";
try {
    $canView = Gate::forUser($user)->allows('order-view');
    $canCreate = Gate::forUser($user)->allows('order-create');
    $canUpdate = Gate::forUser($user)->allows('order-update');
    $canDelete = Gate::forUser($user)->allows('order-delete');
    
    echo "order-view: " . ($canView ? "✅ ALLOWED" : "❌ DENIED") . "\n";
    echo "order-create: " . ($canCreate ? "✅ ALLOWED" : "❌ DENIED") . "\n";
    echo "order-update: " . ($canUpdate ? "✅ ALLOWED" : "❌ DENIED") . "\n";
    echo "order-delete: " . ($canDelete ? "✅ ALLOWED" : "❌ DENIED") . "\n";
} catch (Exception $e) {
    echo "❌ Error checking gates: " . $e->getMessage() . "\n";
}

// 6. Cek middleware requirements
echo "\n=== MIDDLEWARE REQUIREMENTS CHECK ===\n";

// Check if user is approved
$isApproved = $user->status === 'approved';
echo "User Approved: " . ($isApproved ? "✅ YES" : "❌ NO ({$user->status})") . "\n";

// Check if karyawan exists (EnsureKaryawanPresent middleware)
$hasKaryawan = !is_null($karyawan);
echo "Has Karyawan: " . ($hasKaryawan ? "✅ YES" : "❌ NO") . "\n";

// Check crew checklist if karyawan exists
if ($hasKaryawan && $karyawan->pekerjaan && strpos(strtolower($karyawan->pekerjaan), 'abk') !== false) {
    $hasCrewChecklist = !is_null($karyawan->crew_checklist_completed_at ?? null);
    echo "Crew Checklist Complete: " . ($hasCrewChecklist ? "✅ YES" : "❌ NO") . "\n";
} else {
    echo "Crew Checklist: ✅ NOT REQUIRED (bukan ABK)\n";
}

echo "\n=== SUMMARY ===\n";
if (!$isApproved) {
    echo "🚫 MAIN ISSUE: User status is '{$user->status}', harus 'approved' untuk akses order\n";
}
if (!$hasKaryawan) {
    echo "🚫 MAIN ISSUE: User tidak memiliki data karyawan\n";
}
if ($orderPermissions->count() == 0) {
    echo "🚫 MAIN ISSUE: User tidak memiliki permission order\n";
}

echo "\nScript completed.\n";