<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

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
    }
}
