<?php
require_once 'vendor/autoload.php';
use Illuminate\Http\Request;
use App\Models\Permission;

try {
    $permissions = Permission::where('name', 'like', '%master-mobil%')->get();
    echo "=== PERMISSIONS UNTUK MASTER-MOBIL ===\n";
    foreach($permissions as $perm) {
        echo "ID: {$perm->id}, Name: {$perm->name}, Guard: {$perm->guard_name}\n";
    }

    echo "\n=== CEK APPROVE PERMISSION KHUSUS ===\n";
    $approvePerm = Permission::where('name', 'master-mobil.approve')->first();
    if($approvePerm) {
        echo "APPROVE PERMISSION DITEMUKAN:\n";
        echo "ID: {$approvePerm->id}, Name: {$approvePerm->name}\n";
    } else {
        echo "TIDAK ADA APPROVE PERMISSION UNTUK MASTER-MOBIL\n";
    }

} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
