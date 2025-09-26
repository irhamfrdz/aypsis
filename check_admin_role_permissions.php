<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Try different column names for admin user
$possibleColumns = ['name', 'username', 'email'];
$user = null;

foreach ($possibleColumns as $column) {
    try {
        $user = App\Models\User::where($column, 'admin')->first();
        if ($user) {
            echo "Found admin user using column: $column\n";
            break;
        }
    } catch (Exception $e) {
        // Continue to next column
    }
}

if (!$user) {
    echo "Admin user not found with any of these columns: " . implode(', ', $possibleColumns) . "\n";
    echo "Available users:\n";
    $allUsers = App\Models\User::take(5)->get();
    foreach ($allUsers as $u) {
        echo "- ID: {$u->id}, Email: {$u->email}\n";
    }
    exit;
}
    echo "User admin found\n";
    echo "User roles: ";
    foreach ($user->roles as $role) {
        echo $role->name . ', ';
    }
    echo "\n";

    $hasRoleAdmin = $user->hasRole('admin');
    echo "Has admin role: " . ($hasRoleAdmin ? 'YES' : 'NO') . "\n";

    if ($hasRoleAdmin) {
        $adminRole = $user->roles()->where('name', 'admin')->first();
        echo "Admin role permissions count: " . $adminRole->permissions()->count() . "\n";
        $hasApprovalDashboard = $adminRole->permissions()->where('name', 'approval-dashboard')->exists();
        echo "Admin role has approval-dashboard: " . ($hasApprovalDashboard ? 'YES' : 'NO') . "\n";

        // Check if user has the permission directly
        $userHasPermission = $user->permissions()->where('name', 'approval-dashboard')->exists();
        echo "User has approval-dashboard permission directly: " . ($userHasPermission ? 'YES' : 'NO') . "\n";

        // Test the can() method
        echo "User can('approval-dashboard'): " . ($user->can('approval-dashboard') ? 'YES' : 'NO') . "\n";
    }
 else {
    echo "Admin user not found\n";
}

$role = App\Models\Role::where('name', 'admin')->first();
if ($role) {
    echo "\n=== ADMIN ROLE DETAILS ===\n";
    echo "Admin role found\n";
    $hasApprovalDashboard = $role->permissions()->where('name', 'approval-dashboard')->exists();
    echo "Has approval-dashboard permission: " . ($hasApprovalDashboard ? 'YES' : 'NO') . "\n";

    $approvalPermissions = $role->permissions()->where('name', 'like', 'approval%')->get();
    echo "All approval permissions for admin role:\n";
    foreach ($approvalPermissions as $perm) {
        echo "- {$perm->name}\n";
    }
} else {
    echo "Admin role not found\n";
}
