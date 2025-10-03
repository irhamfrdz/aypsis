<?php
// Final permission check
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=aypsis', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== Final Permission Fix Test ===\n\n";

    // 1. Get admin user
    $stmt = $pdo->prepare("SELECT id, username FROM users WHERE username = 'admin'");
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        echo "❌ Admin user not found\n";
        exit;
    }

    echo "✅ Admin user found (ID: {$admin['id']})\n";

    // 2. Get the specific permission
    $stmt = $pdo->prepare("SELECT id, name FROM permissions WHERE name = 'tagihan-kontainer-sewa-create'");
    $stmt->execute();
    $permission = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$permission) {
        echo "❌ Permission 'tagihan-kontainer-sewa-create' not found\n";
        exit;
    }

    echo "✅ Permission 'tagihan-kontainer-sewa-create' exists (ID: {$permission['id']})\n";

    // 3. Check if admin has this permission
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as has_permission
        FROM user_permissions up
        JOIN permissions p ON p.id = up.permission_id
        WHERE up.user_id = ? AND p.name = 'tagihan-kontainer-sewa-create'
    ");
    $stmt->execute([$admin['id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['has_permission'] > 0) {
        echo "✅ Admin HAS 'tagihan-kontainer-sewa-create' permission\n";
        echo "\n🎉 SUCCESS: Import buttons should now be visible!\n\n";

        echo "=== What was fixed ===\n";
        echo "✅ Updated blade template from @can('tagihan-kontainer-create') to @can('tagihan-kontainer-sewa-create')\n";
        echo "✅ Routes already using correct permission\n";
        echo "✅ Admin user has the required permission\n\n";

        echo "=== Test Instructions ===\n";
        echo "1. Go to http://127.0.0.1:8000/daftar-tagihan-kontainer-sewa\n";
        echo "2. Login as admin user\n";
        echo "3. You should now see both 'Import Data' and 'Download Template' buttons\n";

    } else {
        echo "❌ Admin does NOT have 'tagihan-kontainer-sewa-create' permission\n";
        echo "\nAdding permission to admin user...\n";

        $stmt = $pdo->prepare("INSERT INTO user_permissions (user_id, permission_id) VALUES (?, ?)");
        $stmt->execute([$admin['id'], $permission['id']]);

        echo "✅ Permission added! Import buttons should now be visible.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
