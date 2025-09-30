<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
	/**
	 * The policy mappings for the application.
	 *
	 * @var array
	 */
	protected $policies = [
		// 'App\\Models\\Model' => 'App\\Policies\\ModelPolicy',
	];

	/**
	 * Register any authentication / authorization services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->registerPolicies();

		// Gate used by routes that require 'approval-dashboard'
		// Allow users who have any related approval permissions (including approval-tugas-i/ii)
		Gate::define('approval-dashboard', function ($user) {
			if (!$user) return false;

			// Admin shortcut if you use roles
			if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
				return true;
			}

			$perms = [
				'approval-dashboard',
				'approval',
				'approval-view',
				'approval-approve',
				'approval-print',
				// Approval Tugas I
				'approval-tugas-i-view',
				'approval-tugas-i-approve',
				'approval-tugas-i-print',
				'approval-tugas-i-export',
				// Approval Tugas II
				'approval-tugas-ii-view',
				'approval-tugas-ii-approve',
				'approval-tugas-ii-print',
				'approval-tugas-ii-export',
				// Other related permission
				'permohonan.approve'
			];

			foreach ($perms as $p) {
				if ($user->can($p)) {
					return true;
				}
			}

			return false;
		});
	}
}
