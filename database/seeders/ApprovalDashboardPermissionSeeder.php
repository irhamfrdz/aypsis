<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ApprovalDashboardPermissionSeeder extends Seeder
{
    public function run()
    {
        // Create permission if it doesn't exist
        $permission = Permission::firstOrCreate(['name' => 'approval-dashboard']);

        // Assign to admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permission);
            echo "Permission 'approval-dashboard' assigned to admin role\n";
        }

        // Also assign to specific users
        $adminUsers = \App\Models\User::whereIn('username', ['admin', 'test1', 'user_admin'])->get();
        foreach ($adminUsers as $user) {
            $user->givePermissionTo($permission);
            echo "Permission 'approval-dashboard' assigned to user: " . $user->username . "\n";
        }
    }
}
