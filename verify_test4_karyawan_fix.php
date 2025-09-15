<?php

try {
    // Database configuration
    $dbConfig = [
        'host' => '127.0.0.1',
        'port' => 3306,
        'database' => 'aypsis',
        'username' => 'root',
        'password' => ''
    ];

    // Connect to database
    $pdo = new PDO(
        "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']}",
        $dbConfig['username'],
        $dbConfig['password']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "🔍 VERIFICATION: Checking test4's permissions after fix...\n\n";

    // Find user test4
    $stmt = $pdo->prepare("SELECT id, username FROM users WHERE username = ?");
    $stmt->execute(['test4']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "❌ User test4 not found!\n";
        exit(1);
    }

    echo "✅ Found user: {$user['username']} (ID: {$user['id']})\n\n";

    // Get all permissions for test4
    $stmt = $pdo->prepare("
        SELECT p.id, p.name, p.description
        FROM user_permissions up
        JOIN permissions p ON up.permission_id = p.id
        WHERE up.user_id = ?
        ORDER BY p.name
    ");
    $stmt->execute([$user['id']]);
    $permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "📋 CURRENT PERMISSIONS FOR TEST4:\n";
    echo str_repeat("=", 50) . "\n";
    foreach ($permissions as $perm) {
        echo "• {$perm['name']}\n";
    }
    echo str_repeat("=", 50) . "\n\n";

    // Check specifically for karyawan permissions
    $karyawanPermissions = array_filter($permissions, function($perm) {
        return strpos($perm['name'], 'karyawan') !== false;
    });

    echo "🏢 KARYAWAN-SPECIFIC PERMISSIONS:\n";
    echo str_repeat("-", 40) . "\n";
    if (count($karyawanPermissions) > 0) {
        foreach ($karyawanPermissions as $perm) {
            echo "✅ {$perm['name']}\n";
        }
    } else {
        echo "❌ No karyawan permissions found!\n";
    }
    echo str_repeat("-", 40) . "\n\n";

    // Critical check for sidebar display
    $hasMasterKaryawan = false;
    $hasKaryawanView = false;
    $hasKaryawanCreate = false;
    $hasKaryawanUpdate = false;
    $hasKaryawanDelete = false;

    foreach ($permissions as $perm) {
        switch ($perm['name']) {
            case 'master-karyawan':
                $hasMasterKaryawan = true;
                break;
            case 'master-karyawan.view':
                $hasKaryawanView = true;
                break;
            case 'master-karyawan.create':
                $hasKaryawanCreate = true;
                break;
            case 'master-karyawan.update':
                $hasKaryawanUpdate = true;
                break;
            case 'master-karyawan.delete':
                $hasKaryawanDelete = true;
                break;
        }
    }

    echo "🎯 SIDEBAR VISIBILITY ANALYSIS:\n";
    echo str_repeat("=", 50) . "\n";
    echo "Main permission (required for sidebar): " . ($hasMasterKaryawan ? "✅ master-karyawan" : "❌ MISSING master-karyawan") . "\n";
    echo "View permission: " . ($hasKaryawanView ? "✅ master-karyawan.view" : "❌ master-karyawan.view") . "\n";
    echo "Create permission: " . ($hasKaryawanCreate ? "✅ master-karyawan.create" : "❌ master-karyawan.create") . "\n";
    echo "Update permission: " . ($hasKaryawanUpdate ? "✅ master-karyawan.update" : "❌ master-karyawan.update") . "\n";
    echo "Delete permission: " . ($hasKaryawanDelete ? "✅ master-karyawan.delete" : "❌ master-karyawan.delete") . "\n";
    echo str_repeat("=", 50) . "\n\n";

    // Final verdict
    echo "🏁 FINAL VERDICT:\n";
    if ($hasMasterKaryawan) {
        echo "🎉 SUCCESS! User test4 has the required 'master-karyawan' permission.\n";
        echo "   The Karyawan menu SHOULD now appear in the sidebar.\n\n";

        echo "📝 NEXT STEPS:\n";
        echo "1. Log in as test4 in the application\n";
        echo "2. Check the sidebar - you should see 'Karyawan' menu item\n";
        echo "3. If still not visible, try clearing browser cache or logging out/in\n";
        echo "4. The sidebar logic in app.blade.php checks: @if(\$user && \$user->can('master-karyawan'))\n";
    } else {
        echo "❌ FAILURE! User test4 is still missing the 'master-karyawan' permission.\n";
        echo "   The Karyawan menu will NOT appear in the sidebar.\n";
    }

    echo "\n🔧 TROUBLESHOOTING:\n";
    echo "• If menu still doesn't appear, check browser developer console for JavaScript errors\n";
    echo "• Verify that the sidebar template (app.blade.php) is using the correct permission check\n";
    echo "• Check if there are any caching issues with Laravel's permission system\n";

} catch (Exception $e) {
    echo '❌ Database error: ' . $e->getMessage() . "\n";
}

?>
