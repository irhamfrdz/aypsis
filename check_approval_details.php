<?php

$conn = new mysqli('localhost', 'root', '', 'aypsis');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

try {
    // Periksa struktur tabel surat_jalan_approvals
    echo "=== STRUKTUR TABEL SURAT_JALAN_APPROVALS ===\n";
    $result = $conn->query("DESCRIBE surat_jalan_approvals");
    while ($row = $result->fetch_assoc()) {
        echo "{$row['Field']} - {$row['Type']} - {$row['Null']} - {$row['Key']}\n";
    }

    // Ambil data approval terbaru
    echo "\n=== DATA APPROVAL TERBARU ===\n";
    $query = "SELECT
        id,
        surat_jalan_id,
        approval_level,
        status,
        approved_by,
        approved_at,
        approval_notes,
        created_at,
        updated_at
    FROM surat_jalan_approvals
    ORDER BY created_at DESC
    LIMIT 5";

    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "ID: {$row['id']}\n";
            echo "Surat Jalan ID: {$row['surat_jalan_id']}\n";
            echo "Approval Level: {$row['approval_level']}\n";
            echo "Status: {$row['status']}\n";
            echo "Approved By: " . ($row['approved_by'] ?: 'None') . "\n";
            echo "Approved At: " . ($row['approved_at'] ?: 'None') . "\n";
            echo "Notes: " . ($row['approval_notes'] ?: 'None') . "\n";
            echo "Created: {$row['created_at']}\n";
            echo "Updated: {$row['updated_at']}\n";
            echo "------------------------\n";
        }
    } else {
        echo "Tidak ada data approval\n";
    }

    // Periksa relasi antara pranota dan approval melalui surat jalan
    echo "\n=== RELASI PRANOTA DAN APPROVAL MELALUI SURAT JALAN ===\n";
    $relationQuery = "SELECT
        p.id as pranota_id,
        p.nomor_pranota,
        p.status as pranota_status,
        sj.id as surat_jalan_id,
        sj.no_surat_jalan,
        a.id as approval_id,
        a.approval_level,
        a.status as approval_status,
        a.approved_by,
        a.approved_at
    FROM pranota_surat_jalans p
    LEFT JOIN pranota_surat_jalan_items psi ON p.id = psi.pranota_surat_jalan_id
    LEFT JOIN surat_jalans sj ON psi.surat_jalan_id = sj.id
    LEFT JOIN surat_jalan_approvals a ON sj.id = a.surat_jalan_id
    ORDER BY p.created_at DESC
    LIMIT 10";

    $relationResult = $conn->query($relationQuery);

    while ($row = $relationResult->fetch_assoc()) {
        echo "Pranota ID: {$row['pranota_id']}\n";
        echo "Nomor Pranota: {$row['nomor_pranota']}\n";
        echo "Pranota Status: {$row['pranota_status']}\n";
        echo "Surat Jalan ID: " . ($row['surat_jalan_id'] ?: 'None') . "\n";
        echo "No Surat Jalan: " . ($row['no_surat_jalan'] ?: 'None') . "\n";
        echo "Approval ID: " . ($row['approval_id'] ?: 'None') . "\n";
        echo "Approval Level: " . ($row['approval_level'] ?: 'None') . "\n";
        echo "Approval Status: " . ($row['approval_status'] ?: 'None') . "\n";
        echo "Approved By: " . ($row['approved_by'] ?: 'None') . "\n";
        echo "Approved At: " . ($row['approved_at'] ?: 'None') . "\n";
        echo "------------------------\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

$conn->close();
?>
