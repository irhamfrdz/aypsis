<?php
$conn = new mysqli('localhost', 'root', '', 'aypsis');
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Permissions to keep (used in routes)
$keepPermissions = [
    'master-coa',
    'master-coa-view',
    'master-coa-create',
    'master-coa-update',
    'master-coa-delete'
];

// Get all COA permissions except the ones to keep
$placeholders = str_repeat('?,', count($keepPermissions) - 1) . '?';
$stmt = $conn->prepare("SELECT id, name FROM permissions WHERE name LIKE '%coa%' AND name NOT IN ($placeholders)");
$stmt->bind_param(str_repeat('s', count($keepPermissions)), ...$keepPermissions);
$stmt->execute();
$result = $stmt->get_result();

$toDelete = [];
while($row = $result->fetch_assoc()) {
    $toDelete[] = $row;
}

echo 'Permissions to delete:' . PHP_EOL;
foreach($toDelete as $perm) {
    echo '- ID: ' . $perm['id'] . ', Name: ' . $perm['name'] . PHP_EOL;
}

// Delete unused permissions
if (!empty($toDelete)) {
    $ids = array_column($toDelete, 'id');
    $idsString = implode(',', $ids);

    // Remove from permission_role
    $conn->query("DELETE FROM permission_role WHERE permission_id IN ($idsString)");
    echo 'Removed from permission_role: ' . $conn->affected_rows . ' records' . PHP_EOL;

    // Remove from user_permissions
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
