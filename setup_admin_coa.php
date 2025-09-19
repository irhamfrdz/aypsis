<?php
$conn = new mysqli('localhost', 'root', '', 'aypsis');
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Get admin user
$result = $conn->query("SELECT id, username FROM users WHERE username = 'admin'");
$admin = $result->fetch_assoc();

if (!$admin) {
    die('Admin user not found!' . PHP_EOL);
}

echo 'Admin user found: ' . $admin['username'] . ' (ID: ' . $admin['id'] . ')' . PHP_EOL;

// Get COA permissions
$result = $conn->query("SELECT id, name FROM permissions WHERE name LIKE 'master-coa-%'");
$permissions = [];
while($row = $result->fetch_assoc()) {
    $permissions[] = $row;
}

echo PHP_EOL . 'COA permissions:' . PHP_EOL;
foreach($permissions as $perm) {
    echo '- ' . $perm['name'] . ' (ID: ' . $perm['id'] . ')' . PHP_EOL;
}

// Check current permissions for admin
echo PHP_EOL . 'Checking current COA permissions for admin...' . PHP_EOL;
foreach($permissions as $perm) {
    $result = $conn->query("SELECT COUNT(*) as count FROM user_permissions WHERE user_id = {$admin['id']} AND permission_id = {$perm['id']}");
    $count = $result->fetch_assoc()['count'];
    echo '- ' . $perm['name'] . ': ' . ($count > 0 ? 'YES' : 'NO') . PHP_EOL;

    // If doesn't have permission, add it
    if ($count == 0) {
        $conn->query("INSERT INTO user_permissions (user_id, permission_id, created_at, updated_at) VALUES ({$admin['id']}, {$perm['id']}, NOW(), NOW())");
        echo '  -> Added permission' . PHP_EOL;
    }
}

echo PHP_EOL . 'Admin COA permissions setup completed!' . PHP_EOL;

$conn->close();
?>
