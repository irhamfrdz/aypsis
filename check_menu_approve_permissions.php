<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;
use App\Models\Permission;

try {
    // Cek permissions yang mungkin memiliki approve
    $modulesToCheck = [
        'dashboard',
        'tagihan-kontainer',
        'pranota-supir',
        'pembayaran-pranota-supir',
        'permohonan',
        'user-approval'
    ];

    echo "=== CEK APPROVE PERMISSIONS UNTUK MENU-MENU ===\n";

    foreach ($modulesToCheck as $module) {
        echo "\n--- {$module} ---\n";

        // Cek main permission
        $mainPerm = Permission::where('name', $module)->first();
        if ($mainPerm) {
            echo "Main: {$mainPerm->name} (ID: {$mainPerm->id})\n";
        }

        // Cek approve permission dengan berbagai pola
        $approvePatterns = [
            $module . '-approve',
            $module . '.approve',
            'approve-' . $module,
            $module . '_approve'
        ];

        $foundApprove = false;
        foreach ($approvePatterns as $pattern) {
            $approvePerm = Permission::where('name', $pattern)->first();
            if ($approvePerm) {
                echo "APPROVE FOUND: {$approvePerm->name} (ID: {$approvePerm->id})\n";
                $foundApprove = true;
            }
        }

        if (!$foundApprove) {
            echo "NO APPROVE PERMISSION FOUND\n";
        }
    }

    // Cek juga permissions yang terkait dengan approval
    echo "\n=== PERMISSIONS TERKAIT APPROVAL ===\n";
    $approvalPerms = Permission::where('name', 'like', '%approval%')
        ->orWhere('name', 'like', '%approve%')
        ->get();

    foreach ($approvalPerms as $perm) {
        echo "ID: {$perm->id}, Name: {$perm->name}\n";
    }

} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
