<?php

$conn = new mysqli('localhost', 'root', '', 'aypsis');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "🎯 TESTING FINAL STATUS DISPLAY LOGIC\n";
echo "=====================================\n\n";

// Test untuk pranota 1
$pranotaId = 1;
echo "📋 TESTING PRANOTA ID: {$pranotaId}\n";

// Status pranota
$pranotaQuery = "SELECT nomor_pranota, status FROM pranota_surat_jalans WHERE id = {$pranotaId}";
$pranotaResult = $conn->query($pranotaQuery);
$pranota = $pranotaResult->fetch_assoc();

echo "Nomor: {$pranota['nomor_pranota']}\n";
echo "Original Status: {$pranota['status']}\n";

// Cek approval status
$query = "SELECT
    sj.id as surat_jalan_id,
    sj.no_surat_jalan,
    CASE
        WHEN a.status = 'approved' THEN 'approved'
        WHEN a.id IS NOT NULL THEN 'pending'
        ELSE 'draft'
    END as approval_status
FROM pranota_surat_jalan_items psi
JOIN surat_jalans sj ON psi.surat_jalan_id = sj.id
LEFT JOIN surat_jalan_approvals a ON sj.id = a.surat_jalan_id
WHERE psi.pranota_surat_jalan_id = {$pranotaId}
ORDER BY sj.id";

$result = $conn->query($query);

$hasApproval = false;
$allApproved = true;
$approvalStatuses = [];

echo "\n📄 SURAT JALAN APPROVAL STATUS:\n";
while ($row = $result->fetch_assoc()) {
    $status = $row['approval_status'];
    $approvalStatuses[] = $status;

    echo "  • {$row['no_surat_jalan']}: {$status}\n";

    if ($status !== 'draft') {
        $hasApproval = true;
    }
    if ($status !== 'approved') {
        $allApproved = false;
    }
}

echo "\n🔍 LOGIC CALCULATION:\n";
echo "Has Approval: " . ($hasApproval ? 'Yes' : 'No') . "\n";
echo "All Approved: " . ($allApproved ? 'Yes' : 'No') . "\n";

echo "\n🎨 FINAL STATUS DISPLAY:\n";
if ($pranota['status'] == 'paid') {
    echo "Status: 💰 DIBAYAR (green badge)\n";
} elseif ($pranota['status'] == 'cancelled') {
    echo "Status: ❌ DIBATALKAN (red badge)\n";
} elseif ($hasApproval && $allApproved) {
    echo "Status: ✅ APPROVED (green badge)\n";
} elseif ($hasApproval) {
    echo "Status: ⏳ PENDING APPROVAL (yellow badge)\n";
} else {
    echo "Status: 📝 DRAFT (gray badge)\n";
}

echo "\n" . str_repeat("=", 50) . "\n\n";

// Test untuk pranota 2
$pranotaId = 2;
echo "📋 TESTING PRANOTA ID: {$pranotaId}\n";

$pranotaQuery = "SELECT nomor_pranota, status FROM pranota_surat_jalans WHERE id = {$pranotaId}";
$pranotaResult = $conn->query($pranotaQuery);
$pranota = $pranotaResult->fetch_assoc();

echo "Nomor: {$pranota['nomor_pranota']}\n";
echo "Original Status: {$pranota['status']}\n";

$query = "SELECT
    sj.id as surat_jalan_id,
    sj.no_surat_jalan,
    CASE
        WHEN a.status = 'approved' THEN 'approved'
        WHEN a.id IS NOT NULL THEN 'pending'
        ELSE 'draft'
    END as approval_status
FROM pranota_surat_jalan_items psi
JOIN surat_jalans sj ON psi.surat_jalan_id = sj.id
LEFT JOIN surat_jalan_approvals a ON sj.id = a.surat_jalan_id
WHERE psi.pranota_surat_jalan_id = {$pranotaId}
ORDER BY sj.id";

$result = $conn->query($query);

$hasApproval = false;
$allApproved = true;

echo "\n📄 SURAT JALAN APPROVAL STATUS:\n";
while ($row = $result->fetch_assoc()) {
    $status = $row['approval_status'];

    echo "  • {$row['no_surat_jalan']}: {$status}\n";

    if ($status !== 'draft') {
        $hasApproval = true;
    }
    if ($status !== 'approved') {
        $allApproved = false;
    }
}

echo "\n🔍 LOGIC CALCULATION:\n";
echo "Has Approval: " . ($hasApproval ? 'Yes' : 'No') . "\n";
echo "All Approved: " . ($allApproved ? 'Yes' : 'No') . "\n";

echo "\n🎨 FINAL STATUS DISPLAY:\n";
if ($pranota['status'] == 'paid') {
    echo "Status: 💰 DIBAYAR (green badge)\n";
} elseif ($pranota['status'] == 'cancelled') {
    echo "Status: ❌ DIBATALKAN (red badge)\n";
} elseif ($hasApproval && $allApproved) {
    echo "Status: ✅ APPROVED (green badge) - PROBLEM SOLVED! ✨\n";
} elseif ($hasApproval) {
    echo "Status: ⏳ PENDING APPROVAL (yellow badge)\n";
} else {
    echo "Status: 📝 DRAFT (gray badge)\n";
}

$conn->close();
?>
