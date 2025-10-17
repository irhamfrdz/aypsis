<?php

$conn = new mysqli('localhost', 'root', '', 'aypsis');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

try {
    echo "=== TESTING STATUS APPROVAL LOGIC ===\n";

    // Ambil pranota dengan surat jalan yang memiliki approval
    $query = "SELECT
        p.id as pranota_id,
        p.nomor_pranota,
        p.status as pranota_status,
        sj.id as surat_jalan_id,
        sj.no_surat_jalan,
        a.id as approval_id,
        a.status as approval_status
    FROM pranota_surat_jalans p
    JOIN pranota_surat_jalan_items psi ON p.id = psi.pranota_surat_jalan_id
    JOIN surat_jalans sj ON psi.surat_jalan_id = sj.id
    LEFT JOIN surat_jalan_approvals a ON sj.id = a.surat_jalan_id
    WHERE p.id = 2
    ORDER BY sj.id";

    $result = $conn->query($query);

    $pranotaStatus = null;
    $allApproved = true;
    $hasApproval = false;
    $suratJalanCount = 0;
    $approvedCount = 0;

    echo "Pranota ID: 2 (PSJ-1025-000005)\n";
    echo "Surat Jalan yang terkait:\n";

    while ($row = $result->fetch_assoc()) {
        $pranotaStatus = $row['pranota_status'];
        $suratJalanCount++;

        echo "  - SJ ID: {$row['surat_jalan_id']}, No: {$row['no_surat_jalan']}\n";
        echo "    Approval ID: " . ($row['approval_id'] ?: 'None') . "\n";
        echo "    Approval Status: " . ($row['approval_status'] ?: 'None') . "\n";

        if ($row['approval_id']) {
            $hasApproval = true;
            if ($row['approval_status'] === 'approved') {
                $approvedCount++;
                echo "    ✅ Approved\n";
            } else {
                $allApproved = false;
                echo "    ⏳ Not approved yet\n";
            }
        } else {
            $allApproved = false;
            echo "    ❌ No approval record\n";
        }
        echo "\n";
    }

    echo "=== SUMMARY ===\n";
    echo "Pranota Original Status: {$pranotaStatus}\n";
    echo "Total Surat Jalan: {$suratJalanCount}\n";
    echo "Approved Surat Jalan: {$approvedCount}\n";
    echo "Has Approval: " . ($hasApproval ? 'Yes' : 'No') . "\n";
    echo "All Approved: " . ($allApproved ? 'Yes' : 'No') . "\n";

    // Logika status yang akan ditampilkan di view
    echo "\n=== STATUS YANG AKAN DITAMPILKAN ===\n";
    if ($pranotaStatus == 'paid') {
        echo "Status: Dibayar (green)\n";
    } elseif ($pranotaStatus == 'cancelled') {
        echo "Status: Dibatalkan (red)\n";
    } elseif ($hasApproval && $allApproved) {
        echo "Status: Approved (green) ✅\n";
    } elseif ($hasApproval) {
        echo "Status: Pending Approval (yellow) ⏳\n";
    } else {
        echo "Status: Draft (gray)\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

$conn->close();
?>
