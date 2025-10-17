<?php

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'aypsis';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

try {
    // Ambil data pranota surat jalan terbaru
    $query = "SELECT
        id,
        nomor_pranota,
        status,
        tanggal_pranota,
        created_at,
        updated_at
    FROM pranota_surat_jalans
    ORDER BY created_at DESC
    LIMIT 5";

    $result = $conn->query($query);

    echo "=== STATUS PRANOTA SURAT JALAN TERBARU ===\n";
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['id']}\n";
        echo "Nomor: {$row['nomor_pranota']}\n";
        echo "Status: {$row['status']}\n";
        echo "Tanggal: {$row['tanggal_pranota']}\n";
        echo "Created: {$row['created_at']}\n";
        echo "Updated: {$row['updated_at']}\n";
        echo "------------------------\n";
    }

    // Periksa tabel approval surat jalan
    $approvalQuery = "SELECT
        id,
        pranota_surat_jalan_id,
        status as approval_status,
        approved_by,
        approved_at,
        catatan,
        created_at,
        updated_at
    FROM approval_surat_jalans
    ORDER BY created_at DESC
    LIMIT 5";

    $approvalResult = $conn->query($approvalQuery);

    echo "\n=== STATUS APPROVAL SURAT JALAN TERBARU ===\n";
    while ($row = $approvalResult->fetch_assoc()) {
        echo "ID: {$row['id']}\n";
        echo "Pranota ID: {$row['pranota_surat_jalan_id']}\n";
        echo "Approval Status: {$row['approval_status']}\n";
        echo "Approved By: {$row['approved_by']}\n";
        echo "Approved At: {$row['approved_at']}\n";
        echo "Catatan: " . ($row['catatan'] ?: 'None') . "\n";
        echo "Created: {$row['created_at']}\n";
        echo "Updated: {$row['updated_at']}\n";
        echo "------------------------\n";
    }

    // Cek relasi antara pranota dan approval
    $relationQuery = "SELECT
        p.id as pranota_id,
        p.nomor_pranota,
        p.status as pranota_status,
        a.id as approval_id,
        a.status as approval_status,
        a.approved_by,
        a.approved_at
    FROM pranota_surat_jalans p
    LEFT JOIN approval_surat_jalans a ON p.id = a.pranota_surat_jalan_id
    ORDER BY p.created_at DESC
    LIMIT 5";

    $relationResult = $conn->query($relationQuery);

    echo "\n=== RELASI PRANOTA DAN APPROVAL ===\n";
    while ($row = $relationResult->fetch_assoc()) {
        echo "Pranota ID: {$row['pranota_id']}\n";
        echo "Nomor: {$row['nomor_pranota']}\n";
        echo "Pranota Status: {$row['pranota_status']}\n";
        echo "Approval ID: " . ($row['approval_id'] ?: 'None') . "\n";
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
