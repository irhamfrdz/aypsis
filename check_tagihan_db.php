<?php

// Simple script to check tagihan permissions for marlina
echo "=== CHECK TAGIHAN KONTAINER SEWA PERMISSIONS FOR MARLINA ===\n\n";

// Database connection
try {
    $pdo = new PDO('mysql:host=localhost;dbname=aypsis', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Get user marlina
$stmt = $pdo->prepare("SELECT id, name FROM users WHERE username = ?");
$stmt->execute(['marlina']);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User marlina not found!\n";
    exit(1);
}

echo "User: {$user['name']} (ID: {$user['id']})\n\n";

// Check if permission exists
$stmt = $pdo->prepare("SELECT id FROM permissions WHERE name = ?");
$stmt->execute(['tagihan-kontainer-sewa-index']);
$permission = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$permission) {
    echo "Permission 'tagihan-kontainer-sewa-index' does not exist in database!\n";
    exit(1);
}

echo "Permission 'tagihan-kontainer-sewa-index' exists (ID: {$permission['id']})\n";

// Check if user has this permission
$stmt = $pdo->prepare("
    SELECT COUNT(*) as count
    FROM model_has_permissions
    WHERE model_type = 'App\\Models\\User'
    AND model_id = ?
    AND permission_id = ?
");
$stmt->execute([$user['id'], $permission['id']]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

$hasPermission = $result['count'] > 0;
echo "User has permission in database: " . ($hasPermission ? 'YES' : 'NO') . "\n";

// Get all tagihan permissions for this user
$stmt = $pdo->prepare("
    SELECT p.name, p.id as perm_id
    FROM permissions p
    JOIN model_has_permissions mhp ON p.id = mhp.permission_id
    WHERE mhp.model_type = 'App\\Models\\User'
    AND mhp.model_id = ?
    AND p.name LIKE '%tagihan%'
");
$stmt->execute([$user['id']]);
$tagihanPerms = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "\nAll tagihan-related permissions for user:\n";
if (empty($tagihanPerms)) {
    echo "No tagihan permissions found!\n";
} else {
    foreach ($tagihanPerms as $perm) {
        echo "- {$perm['name']} (ID: {$perm['perm_id']})\n";
    }
}

echo "\n=== CONCLUSION ===\n";
if ($hasPermission) {
    echo "✓ User should see the menu\n";
} else {
    echo "✗ User will NOT see the menu - permission issue\n";
}
