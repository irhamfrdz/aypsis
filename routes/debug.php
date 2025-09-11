Route::get('/debug-gate', function () {
    $user = auth()->user() ?? \App\Models\User::first();

    if (!$user) {
        return response()->json(['error' => 'No user found']);
    }

    // Test simple gate
    \Illuminate\Support\Facades\Gate::define('debug-test', function () {
        return true;
    });

    $results = [
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'permissions_count' => $user->permissions->count(),
            'has_dashboard' => $user->hasPermissionTo('dashboard'),
        ],
        'tests' => [
            'simple_gate' => \Illuminate\Support\Facades\Gate::check('debug-test', $user),
            'dashboard_gate' => \Illuminate\Support\Facades\Gate::check('dashboard', $user),
            'user_can_dashboard' => $user->can('dashboard'),
            'gate_has_dashboard' => \Illuminate\Support\Facades\Gate::has('dashboard'),
        ]
    ];

    return response()->json($results);
});
