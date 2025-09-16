<?php

$host = '127.0.0.1';
$dbname = 'aypsis';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Master kontainer permissions:\n";
    $stmt = $pdo->query("SELECT id, name FROM permissions WHERE name LIKE 'master-kontainer%' ORDER BY name");
    $perms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach($perms as $perm) {
        echo "- ID {$perm['id']}: {$perm['name']}\n";
    }

    // Check specific permission
    echo "\nChecking master-kontainer.view:\n";
    $stmt = $pdo->prepare("SELECT id, name FROM permissions WHERE name = ?");
    $stmt->execute(['master-kontainer.view']);
    $perm = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($perm) {
        echo "✓ Found: ID {$perm['id']}, Name: {$perm['name']}\n";
    } else {
        echo "✗ Not found: master-kontainer.view\n";
    }

} catch(PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}

?>
