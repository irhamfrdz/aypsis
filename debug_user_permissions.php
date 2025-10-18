<?php
require 'vendor/autoload.php';

// Simulate Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "=== DEBUG USER PERMISSIONS ===\n";

// Check all admin users
$adminUsers = User::where('username', 'like', '%admin%')->get();

foreach ($adminUsers as $user) {
    echo "\n👤 User: {$user->username} (ID: {$user->id})\n";

    // Check audit-log permissions
    $auditLogView = $user->hasPermissionTo('audit-log-view');
    $auditLogEdit = $user->hasPermissionTo('audit-log-edit');
    $auditLogCreate = $user->hasPermissionTo('audit-log-create');
    $auditLogDelete = $user->hasPermissionTo('audit-log-delete');

    echo "  🔐 Permissions:\n";
    echo "     audit-log-view: " . ($auditLogView ? '✅' : '❌') . "\n";
    echo "     audit-log-edit: " . ($auditLogEdit ? '✅' : '❌') . "\n";
    echo "     audit-log-create: " . ($auditLogCreate ? '✅' : '❌') . "\n";
    echo "     audit-log-delete: " . ($auditLogDelete ? '✅' : '❌') . "\n";

    // Total permissions
    $totalPermissions = $user->permissions->count();
    echo "  📊 Total permissions: {$totalPermissions}\n";
}

echo "\n=== MIDDLEWARE CHECK ===\n";

// Check if middleware might be blocking
echo "Laravel version: " . app()->version() . "\n";
echo "Environment: " . config('app.env') . "\n";

echo "\n=== ROUTE CHECK ===\n";

// Check if route exists
try {
    $route = \Illuminate\Support\Facades\Route::getRoutes()->getByName('audit-logs.model');
    if ($route) {
        echo "✅ Route 'audit-logs.model' exists\n";
        echo "   URI: " . $route->uri() . "\n";
        echo "   Methods: " . implode(', ', $route->methods()) . "\n";
        echo "   Action: " . $route->getActionName() . "\n";
    } else {
        echo "❌ Route 'audit-logs.model' tidak ditemukan\n";
    }
} catch (Exception $e) {
    echo "❌ Error checking route: " . $e->getMessage() . "\n";
}

echo "\n=================================================\n";
