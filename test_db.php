<?php

// Test permission database
$pdo = new PDO('mysql:host=localhost;dbname=aypsis', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Get user marlina
$stmt = $pdo->prepare('SELECT id, username FROM users WHERE username = ?');
$stmt->execute(['marlina']);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo 'User: ' . $user['username'] . ' (ID: ' . $user['id'] . ')' . PHP_EOL;

    // Check permission
    $stmt = $pdo->prepare('SELECT p.name FROM permissions p JOIN model_has_permissions mhp ON p.id = mhp.permission_id WHERE mhp.model_type = ? AND mhp.model_id = ? AND p.name = ?');
    $stmt->execute(['App\\Models\\User', $user['id'], 'tagihan-perbaikan-kontainer-view']);
    $perm = $stmt->fetch(PDO::FETCH_ASSOC);

    echo 'Has tagihan-perbaikan-kontainer-view: ' . ($perm ? 'YES' : 'NO') . PHP_EOL;

    // List all permissions
    $stmt = $pdo->prepare('SELECT p.name FROM permissions p JOIN model_has_permissions mhp ON p.id = mhp.permission_id WHERE mhp.model_type = ? AND mhp.model_id = ?');
    $stmt->execute(['App\\Models\\User', $user['id']]);
    $perms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo 'All permissions:' . PHP_EOL;
    foreach ($perms as $p) {
        echo '- ' . $p['name'] . PHP_EOL;
    }
} else {
    echo 'User not found' . PHP_EOL;
}
