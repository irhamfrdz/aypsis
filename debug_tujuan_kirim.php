<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;
use App\Models\User;

echo "=== CHECKING PERMISSIONS ===\n";
$permissions = Permission::where('name', 'like', '%tujuan-kirim%')->get();
echo "Found " . $permissions->count() . " permissions with 'tujuan-kirim':\n";
foreach($permissions as $perm) {
    echo "- " . $perm->name . "\n";
}

echo "\n=== CHECKING USER ADMIN ===\n";
$userAdmin = User::where('username', 'user_admin')->first();
if($userAdmin) {
    echo "User admin found: " . $userAdmin->username . "\n";
    $allPerms = $userAdmin->permissions()->get();
    echo "Total permissions assigned: " . $allPerms->count() . "\n";

    $tujuanKirimPerms = $allPerms->filter(function($perm) {
        return strpos($perm->name, 'tujuan-kirim') !== false;
    });
    echo "Tujuan kirim permissions: " . $tujuanKirimPerms->count() . "\n";
    foreach($tujuanKirimPerms as $perm) {
        echo "- " . $perm->name . "\n";
    }
} else {
    echo "User admin not found\n";
}

echo "\n=== CHECKING ROUTES ===\n";
$routes = app('router')->getRoutes();
$tujuanKirimRoutes = [];
foreach($routes as $route) {
    $name = $route->getName();
    if($name && strpos($name, 'tujuan-kirim') !== false) {
        $tujuanKirimRoutes[] = $name;
    }
}
echo "Found " . count($tujuanKirimRoutes) . " routes with 'tujuan-kirim':\n";
foreach($tujuanKirimRoutes as $route) {
    echo "- " . $route . "\n";
}
