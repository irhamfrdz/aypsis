<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Permission;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     */
    public function boot()
    {
        // Grant all abilities to users with role 'admin'
        Gate::before(function (User $user, $ability) {
            try {
                if ($user->roles()->where('name', 'admin')->exists()) {
                    return true;
                }
            } catch (\Throwable $e) {
                // in case roles relation not available during certain early boot stages
            }

            return null;
        });

        // Define permission-based gates
        try {
            $permissions = Permission::all();
            foreach ($permissions as $permission) {
                Gate::define($permission->name, function (User $user) use ($permission) {
                    return $user->hasPermissionTo($permission->name);
                });
            }
        } catch (\Throwable $e) {
            // Handle case where database is not available during early boot
        }
    }
}
