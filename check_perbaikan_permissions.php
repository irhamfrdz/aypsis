<?php

// Simple script to check perbaikan-kontainer permissions
echo "=== Checking Perbaikan Kontainer Permissions ===\n";

// Connect to database directly
try {
    $pdo = new PDO('mysql:host=localhost;dbname=aypsis', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check permissions table
    $stmt = $pdo->query("SELECT name FROM permissions WHERE name LIKE '%perbaikan-kontainer%' ORDER BY name");
    $permissions = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "Found " . count($permissions) . " perbaikan-kontainer permissions:\n";
    foreach($permissions as $permission) {
        echo "- " . $permission . "\n";
    }

    // Check if routes exist
    echo "\n=== Checking Routes ===\n";
    $stmt = $pdo->query("SELECT uri, method FROM routes WHERE uri LIKE '%perbaikan-kontainer%' ORDER BY uri");
    $routes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Found " . count($routes) . " perbaikan-kontainer routes:\n";
    foreach($routes as $route) {
        echo "- " . $route['method'] . " " . $route['uri'] . "\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
