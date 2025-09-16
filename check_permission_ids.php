<?php

$host = '127.0.0.1';
$dbname = 'aypsis';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check permission ID 410
    $stmt = $pdo->prepare("SELECT name FROM permissions WHERE id = ?");
    $stmt->execute([410]);
    $perm = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "Permission ID 410: " . ($perm ? $perm['name'] : 'Not found') . "\n";

    // Check what permission IDs are returned for master-karyawan.view
    echo "\nChecking permissions for master-karyawan.view:\n";
    $stmt = $pdo->prepare("SELECT id, name FROM permissions WHERE name = ?");
    $stmt->execute(['master-karyawan.view']);
    $permDash = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($permDash) {
        echo "Found: ID {$permDash['id']}, Name: {$permDash['name']}\n";
    } else {
        echo "master-karyawan.view not found\n";
    }

    // Check what permission IDs are returned for master.karyawan.show
    echo "\nChecking permissions for master.karyawan.show:\n";
    $stmt->execute(['master.karyawan.show']);
    $permDot = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($permDot) {
        echo "Found: ID {$permDot['id']}, Name: {$permDot['name']}\n";
    } else {
        echo "master.karyawan.show not found\n";
    }

} catch(PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}

?>
