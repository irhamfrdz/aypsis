<?php
$conn = new mysqli('localhost', 'root', '', 'aypsis');
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Get all COA permissions with dot format
$result = $conn->query('SELECT id, name FROM permissions WHERE name LIKE "master-coa.%"');
$dotPermissions = [];
while($row = $result->fetch_assoc()) {
    $dotPermissions[] = $row;
}

echo 'Found ' . count($dotPermissions) . ' COA permissions with dot format:' . PHP_EOL;
foreach($dotPermissions as $perm) {
    echo '- ID: ' . $perm['id'] . ', Name: ' . $perm['name'] . PHP_EOL;
}

// Remove role_has_permissions for dot format permissions
if (!empty($dotPermissions)) {
    $ids = array_column($dotPermissions, 'id');
    $idsString = implode(',', $ids);

    // Remove from role_has_permissions (actually permission_role)
    $conn->query("DELETE FROM permission_role WHERE permission_id IN ($idsString)");
    echo 'Removed from permission_role: ' . $conn->affected_rows . ' records' . PHP_EOL;

    // Remove from model_has_permissions (actually user_permissions)
    $conn->query("DELETE FROM user_permissions WHERE permission_id IN ($idsString)");
    echo 'Removed from user_permissions: ' . $conn->affected_rows . ' records' . PHP_EOL;

    // Remove the permissions themselves
    $conn->query("DELETE FROM permissions WHERE id IN ($idsString)");
    echo 'Removed permissions: ' . $conn->affected_rows . ' records' . PHP_EOL;
}

echo PHP_EOL . 'Cleanup completed!' . PHP_EOL;

// Show remaining COA permissions
$result = $conn->query('SELECT name FROM permissions WHERE name LIKE "%coa%" ORDER BY name');
echo PHP_EOL . 'Remaining COA permissions:' . PHP_EOL;
while($row = $result->fetch_assoc()) {
    echo '- ' . $row['name'] . PHP_EOL;
}

$conn->close();
?>
