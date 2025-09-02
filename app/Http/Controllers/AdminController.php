<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route as RouteFacade;
use App\Models\Permission;

class AdminController extends Controller
{
    /**
     * Show a page listing all permissions and registered web routes.
     */
    public function features(Request $request)
    {
        $permissions = Permission::orderBy('name')->get();

        $allRoutes = collect(RouteFacade::getRoutes())->map(function ($r) {
            return [
                'uri' => $r->uri(),
                'name' => $r->getName(),
                'methods' => implode('|', $r->methods()),
                'action' => $r->getActionName(),
                'middleware' => implode('|', $r->gatherMiddleware()),
            ];
        })->filter(function ($r) {
            // hide internal closures and api routes for brevity
            return strpos($r['uri'], 'api') !== 0;
        })->values();

        return view('admin.features', compact('permissions', 'allRoutes'));
    }

    /**
     * Diagnostic: return current authenticated user's roles and permissions as JSON.
     * Only accessible to admin via middleware.
     */
    public function debug(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'not authenticated'], 401);
        }

        $roles = $user->roles()->pluck('name');
        $perms = $user->permissions()->pluck('name');

        $checks = [
            'master-karyawan' => auth()->user()->can('master-karyawan'),
            'master-user' => auth()->user()->can('master-user'),
            'master-pricelist-sewa-kontainer' => auth()->user()->can('master-pricelist-sewa-kontainer'),
            'master-permission' => auth()->user()->can('master-permission'),
        ];

        return response()->json([
            'id' => $user->id,
            'username' => $user->username ?? null,
            'name' => $user->name ?? null,
            'roles' => $roles,
            'permissions' => $perms,
            'gate_checks' => $checks,
        ]);
    }
}
