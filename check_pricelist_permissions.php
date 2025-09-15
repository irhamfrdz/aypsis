<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;
use App\Models\Permission;

try {
    // Cari semua permissions yang terkait dengan pricelist atau sewa kontainer
    $pricelistPermissions = Permission::where('name', 'like', '%pricelist%')
        ->orWhere('name', 'like', '%sewa%')
        ->orWhere('name', 'like', '%kontainer%')
        ->get();

    echo "=== PERMISSIONS TERKAIT PRICELIST/SEWA KONTAINER ===\n";
    if($pricelistPermissions->count() > 0) {
        foreach($pricelistPermissions as $perm) {
            echo "ID: {$perm->id}, Name: {$perm->name}, Guard: {$perm->guard_name}\n";
        }
    } else {
        echo "TIDAK ADA PERMISSIONS YANG TERKAIT DENGAN PRICELIST/SEWA KONTAINER\n";
    }

    echo "\n=== CEK APPROVE PERMISSION KHUSUS ===\n";

    // Cek berbagai kemungkinan nama approve permission
    $approvePatterns = [
        'pricelist.approve',
        'sewa-kontainer.approve',
        'master-pricelist.approve',
        'pricelist-sewa-kontainer.approve'
    ];

    $foundApprove = false;
    foreach($approvePatterns as $pattern) {
        $approvePerm = Permission::where('name', $pattern)->first();
        if($approvePerm) {
            echo "APPROVE PERMISSION DITEMUKAN: {$pattern}\n";
            echo "ID: {$approvePerm->id}, Name: {$approvePerm->name}\n";
            $foundApprove = true;
        }
    }

    if(!$foundApprove) {
        echo "TIDAK ADA APPROVE PERMISSION UNTUK PRICELIST/SEWA KONTAINER\n";
    }

    // Cek semua permissions yang ada di database untuk referensi
    echo "\n=== SEMUA PERMISSIONS DI DATABASE (UNTUK REFERENSI) ===\n";
    $allPermissions = Permission::all();
    foreach($allPermissions as $perm) {
        echo "ID: {$perm->id}, Name: {$perm->name}\n";
    }

} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
