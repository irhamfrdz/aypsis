Route::get('/debug-roles', function () {
    $user = Auth::user() ?? \App\Models\User::first();

    if (!$user) {
        return response()->json(['error' => 'No user found']);
    }

    $results = [
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
        ],
        'roles_test' => []
    ];

    try {
        $roles = $user->roles;
        $results['roles_test']['roles_loaded'] = true;
        $results['roles_test']['roles_count'] = $roles->count();
        $results['roles_test']['roles_names'] = $roles->pluck('name')->toArray();

        // Test the exact query from AuthServiceProvider
        $hasAdminRole = $user->roles()->where('name', 'admin')->exists();
        $results['roles_test']['has_admin_role'] = $hasAdminRole;

    } catch (Exception $e) {
        $results['roles_test']['error'] = $e->getMessage();
        $results['roles_test']['roles_loaded'] = false;
    }

    return response()->json($results);
});
