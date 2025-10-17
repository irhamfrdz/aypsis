<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== CHECKING APPROVAL TUGAS PERMISSIONS ===" . PHP_EOL;

$user = \App\Models\User::where('username', 'admin')->first();
if (!$user) {
    echo "❌ User admin tidak ditemukan" . PHP_EOL;
    exit(1);
}

echo "User: " . $user->username . " (ID: " . $user->id . ")" . PHP_EOL;
echo PHP_EOL . "Checking permissions yang diperlukan untuk melihat menu 'Approval Tugas':" . PHP_EOL;

$approvalTugasPermissions = [
    'approval-view',
    'approval-approve',
    'approval-print',
    'approval-dashboard',
    'approval',
    'permohonan.approve'
];

$hasAnyApprovalTugasPermission = false;
foreach ($approvalTugasPermissions as $permission) {
    $has = $user->can($permission);
    echo "- $permission: " . ($has ? "✅ YA" : "❌ TIDAK") . PHP_EOL;
    if ($has) {
        $hasAnyApprovalTugasPermission = true;
    }
}

echo PHP_EOL . "HASIL: " . PHP_EOL;
if ($hasAnyApprovalTugasPermission) {
    echo "✅ User memiliki minimal 1 permission untuk melihat menu 'Approval Tugas'" . PHP_EOL;
    echo "✅ Menu 'Approval Surat Jalan' SEHARUSNYA terlihat di dalam submenu 'Approval Tugas'" . PHP_EOL;
} else {
    echo "❌ User TIDAK memiliki permission untuk melihat menu 'Approval Tugas'" . PHP_EOL;
    echo "❌ Menu 'Approval Surat Jalan' TIDAK akan terlihat" . PHP_EOL;
    echo PHP_EOL . "SOLUSI: Berikan salah satu permission berikut ke user admin:" . PHP_EOL;
    foreach ($approvalTugasPermissions as $permission) {
        echo "  - $permission" . PHP_EOL;
    }
}

echo PHP_EOL . "=== END CHECK ===" . PHP_EOL;
