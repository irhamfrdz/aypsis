<?php

// Script untuk setup permissions audit log di server
// File: setup_audit_permissions_server.php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔑 SETUP AUDIT LOG PERMISSIONS DI SERVER\n";
echo "=======================================\n\n";

try {
    // 1. Buat permissions jika belum ada
    echo "📋 Checking and creating permissions...\n";

    $permissions = [
        'audit-log-view' => 'Melihat audit log',
        'audit-log-export' => 'Export audit log'
    ];

    $createdPermissions = [];

    foreach ($permissions as $name => $description) {
        $existing = DB::table('permissions')->where('name', $name)->first();

        if (!$existing) {
            $id = DB::table('permissions')->insertGetId([
                'name' => $name,
                'description' => $description,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "✅ Permission '$name' created (ID: $id)\n";
            $createdPermissions[$name] = $id;
        } else {
            echo "ℹ️  Permission '$name' already exists (ID: {$existing->id})\n";
            $createdPermissions[$name] = $existing->id;
        }
    }

    // 2. Cari user admin
    echo "\n👤 Finding admin user...\n";

    $admin = DB::table('users')->where('username', 'admin')->first();
    if (!$admin) {
        // Coba cari dengan email admin
        $admin = DB::table('users')->where('email', 'admin@example.com')->first();
    }

    if (!$admin) {
        echo "❌ User 'admin' tidak ditemukan!\n";
        echo "💡 Silakan buat user admin terlebih dahulu atau ganti username di script ini.\n";

        // Tampilkan semua users yang ada
        $users = DB::table('users')->select('id', 'username', 'email')->limit(5)->get();
        echo "\n📋 Users yang tersedia:\n";
        foreach ($users as $user) {
            echo "   - ID: {$user->id}, Username: {$user->username}, Email: {$user->email}\n";
        }
        exit;
    }

    echo "✅ Admin user found: {$admin->username} (ID: {$admin->id})\n";

    // 3. Assign permissions ke admin
    echo "\n🔗 Assigning permissions to admin...\n";

    foreach ($createdPermissions as $permName => $permId) {
        $existing = DB::table('user_permissions')
            ->where('user_id', $admin->id)
            ->where('permission_id', $permId)
            ->first();

        if (!$existing) {
            DB::table('user_permissions')->insert([
                'user_id' => $admin->id,
                'permission_id' => $permId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "✅ Permission '$permName' assigned to admin\n";
        } else {
            echo "ℹ️  Permission '$permName' sudah ada untuk admin\n";
        }
    }

    // 4. Verify permissions
    echo "\n🔍 Verifying admin permissions...\n";

    $adminPermissions = DB::table('user_permissions')
        ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
        ->where('user_permissions.user_id', $admin->id)
        ->whereIn('permissions.name', ['audit-log-view', 'audit-log-export'])
        ->select('permissions.name', 'permissions.id')
        ->get();

    echo "Admin memiliki permissions:\n";
    foreach ($adminPermissions as $perm) {
        echo "   ✅ {$perm->name} (ID: {$perm->id})\n";
    }

    // 5. Cek table audit_logs
    echo "\n📊 Checking audit_logs table...\n";

    $tableExists = DB::select("SHOW TABLES LIKE 'audit_logs'");
    if (empty($tableExists)) {
        echo "❌ Table 'audit_logs' belum ada!\n";
        echo "💡 Jalankan: php artisan migrate\n";
    } else {
        echo "✅ Table 'audit_logs' sudah ada\n";

        // Cek jumlah audit logs
        $auditCount = DB::table('audit_logs')->count();
        echo "📈 Total audit logs: $auditCount\n";
    }

    // 6. Setup routes info
    echo "\n📍 ROUTES YANG DIPERLUKAN:\n";
    echo str_repeat("=", 40) . "\n";
    echo "Pastikan file routes/web.php memiliki:\n\n";

    $routeCode = "
Route::middleware(['auth'])->group(function () {
    // Audit Log Routes
    Route::get('/audit-logs', [AuditLogController::class, 'index'])
        ->name('audit-logs.index')
        ->middleware('can:audit-log-view');

    Route::get('/audit-logs/{id}', [AuditLogController::class, 'show'])
        ->name('audit-logs.show')
        ->middleware('can:audit-log-view');

    Route::get('/audit-logs/model/data', [AuditLogController::class, 'getModelAuditLogs'])
        ->name('audit-logs.model')
        ->middleware('can:audit-log-view');

    Route::get('/audit-logs/export/csv', [AuditLogController::class, 'export'])
        ->name('audit-logs.export')
        ->middleware('can:audit-log-export');
});
";

    echo $routeCode;

    // 7. Sidebar menu info
    echo "\n🎨 SIDEBAR MENU:\n";
    echo str_repeat("=", 40) . "\n";
    echo "Tambahkan ke sidebar:\n\n";

    $sidebarCode = "
@can('audit-log-view')
<li class=\"nav-item\">
    <a href=\"{{ route('audit-logs.index') }}\" class=\"nav-link\">
        <i class=\"fas fa-history nav-icon\"></i>
        <p>Audit Log</p>
    </a>
</li>
@endcan
";

    echo $sidebarCode;

    echo "\n🎉 SETUP PERMISSIONS SELESAI!\n";
    echo "===============================\n";
    echo "Langkah selanjutnya:\n";
    echo "1. Pastikan routes sudah ditambahkan\n";
    echo "2. Update sidebar menu\n";
    echo "3. Jalankan: php add_auditable_to_all_models.php\n";
    echo "4. Test dengan: php test_audit_log_implementation.php\n";
    echo "5. Clear cache: php artisan cache:clear\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

?>
