<?php

$host = '127.0.0.1';
$dbname = 'aypsis';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check exact permission
    $stmt = $pdo->prepare("SELECT id, name, LENGTH(name) as len FROM permissions WHERE name = ?");
    $stmt->execute(['master-karyawan.view']);
    $perm = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($perm) {
        echo "Found: ID {$perm['id']}, Name: \"{$perm['name']}\", Length: {$perm['len']}\n";
        echo "Hex: " . bin2hex($perm['name']) . "\n";
    } else {
        echo "Not found\n";
    }

    // Check with LIKE
    echo "\nChecking with LIKE:\n";
    $stmt = $pdo->prepare("SELECT id, name FROM permissions WHERE name LIKE ?");
    $stmt->execute(['master-karyawan.view']);
    $perms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach($perms as $p) {
        echo "LIKE found: ID {$p['id']}, Name: \"{$p['name']}\"\n";
    }

    // Check all master-karyawan permissions
    echo "\nAll master-karyawan permissions:\n";
    $stmt = $pdo->query("SELECT id, name FROM permissions WHERE name LIKE 'master-karyawan.%' ORDER BY name");
    $perms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach($perms as $p) {
        echo "- ID {$p['id']}: {$p['name']}\n";
    }

} catch(PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}

?>
