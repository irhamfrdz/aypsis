<?php

// Simple script to check permissions without Laravel bootstrap
$host = '127.0.0.1';
$dbname = 'aypsis';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== Available Permissions ===\n";

    // Get all permissions
    $stmt = $pdo->query("SELECT name FROM permissions ORDER BY name");
    $permissions = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach($permissions as $permission) {
        echo "- " . $permission . "\n";
    }

    echo "\n=== User test4 Permissions ===\n";

    // Get user test4
    $stmt = $pdo->prepare("SELECT id, name FROM users WHERE name = ?");
    $stmt->execute(['test4']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if($user) {
        echo "User ID: " . $user['id'] . "\n";
        echo "User Name: " . $user['name'] . "\n";

        // Get user permissions
        $stmt = $pdo->prepare("
            SELECT p.name
            FROM permissions p
            INNER JOIN user_permissions up ON p.id = up.permission_id
            WHERE up.user_id = ?
            ORDER BY p.name
        ");
        $stmt->execute([$user['id']]);
        $userPermissions = $stmt->fetchAll(PDO::FETCH_COLUMN);

        echo "Permissions: " . implode(', ', $userPermissions) . "\n";

        // Check specific permissions
        $hasMasterKaryawanView = in_array('master-karyawan.view', $userPermissions);
        $hasMasterKaryawanShow = in_array('master.karyawan.show', $userPermissions);

        echo "Has master-karyawan.view: " . ($hasMasterKaryawanView ? 'YES' : 'NO') . "\n";
        echo "Has master.karyawan.show: " . ($hasMasterKaryawanShow ? 'YES' : 'NO') . "\n";

    } else {
        echo "User test4 not found\n";
    }

} catch(PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}

?>
