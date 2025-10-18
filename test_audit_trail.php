<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Karyawan;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

// Login sebagai admin untuk testing
$adminUser = User::where('username', 'admin')->first();
if ($adminUser) {
    Auth::login($adminUser);
    echo "Logged in as: {$adminUser->username}\n";
} else {
    echo "Admin user not found!\n";
    exit;
}

// Test create karyawan
echo "\n=== Testing Audit Trail ===\n";

// Ambil karyawan pertama untuk diupdate
$karyawan = Karyawan::first();

if ($karyawan) {
    echo "Testing update on Karyawan: {$karyawan->nama_lengkap} (ID: {$karyawan->id})\n";

    // Update beberapa field
    $oldNama = $karyawan->nama_panggilan;
    $karyawan->nama_panggilan = $oldNama . ' (Updated)';
    $karyawan->catatan = 'Test audit trail - ' . now()->format('Y-m-d H:i:s');
    $karyawan->save();

    echo "Updated nama_panggilan from '{$oldNama}' to '{$karyawan->nama_panggilan}'\n";

    // Check audit log
    $latestAuditLog = AuditLog::where('auditable_type', 'App\\Models\\Karyawan')
        ->where('auditable_id', $karyawan->id)
        ->orderBy('created_at', 'desc')
        ->first();

    if ($latestAuditLog) {
        echo "\n✅ Audit log created successfully!\n";
        echo "- ID: {$latestAuditLog->id}\n";
        echo "- Action: {$latestAuditLog->action}\n";
        echo "- User: {$latestAuditLog->getUserDisplayName()}\n";
        echo "- Module: {$latestAuditLog->module}\n";
        echo "- Description: {$latestAuditLog->description}\n";
        echo "- IP Address: {$latestAuditLog->ip_address}\n";
        echo "- Timestamp: {$latestAuditLog->created_at}\n";

        if ($latestAuditLog->old_values && $latestAuditLog->new_values) {
            echo "\n📝 Changes detected:\n";
            $changes = $latestAuditLog->getFormattedChanges();
            if ($changes) {
                foreach ($changes as $change) {
                    echo "  - {$change['field']}: '{$change['old']}' → '{$change['new']}'\n";
                }
            }
        }

    } else {
        echo "\n❌ No audit log found!\n";
    }

    // Rollback perubahan
    $karyawan->nama_panggilan = $oldNama;
    $karyawan->catatan = null;
    $karyawan->save();

    echo "\n✅ Changes rolled back\n";

} else {
    echo "No karyawan found for testing!\n";
}

echo "\nDone!\n";
