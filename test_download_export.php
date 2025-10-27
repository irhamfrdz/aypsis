<?php

require_once 'vendor/autoload.php';

use App\Models\Karyawan;

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TESTING DOWNLOADABLE EXPORT ===\n";

try {
    // Clear any potential cache to ensure fresh data
    if (function_exists('opcache_reset')) {
        opcache_reset();
    }
    
    $columns = [
        'nik','nama_panggilan','nama_lengkap','plat','email','ktp','kk','alamat','rt_rw','kelurahan','kecamatan','kabupaten','provinsi','kode_pos','alamat_lengkap','tempat_lahir','tanggal_lahir','no_hp','jenis_kelamin','status_perkawinan','agama','divisi','pekerjaan','tanggal_masuk','tanggal_berhenti','tanggal_masuk_sebelumnya','tanggal_berhenti_sebelumnya','catatan','status_pajak','nama_bank','bank_cabang','akun_bank','atas_nama','jkn','no_ketenagakerjaan','cabang','nik_supervisor','supervisor'
    ];
    
    $fileName = 'test_export_' . date('Ymd_His') . '.csv';
    
    // Generate actual CSV file
    $callback = function() use ($columns) {
        $out = fopen('php://output', 'w');

        // Write UTF-8 BOM for Excel recognition
        fwrite($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Write header row with semicolon delimiter for Excel CSV compatibility
        fwrite($out, implode(";", $columns) . "\r\n");

        // Stream rows for actual export with proper formatting - use fresh() for latest data
        Karyawan::chunk(200, function($rows) use ($out, $columns) {
            foreach ($rows as $r) {
                // Get fresh instance to ensure we have the latest data
                $r = $r->fresh();
                
                $line = [];
                foreach ($columns as $col) {
                    $val = $r->{$col} ?? '';

                    // Format dates to dd/mmm/yyyy for Excel export
                    if ($val instanceof \DateTimeInterface) {
                        $val = $val->format('d/M/Y');
                    } elseif (in_array($col, ['tanggal_lahir', 'tanggal_masuk', 'tanggal_berhenti', 'tanggal_masuk_sebelumnya', 'tanggal_berhenti_sebelumnya'])) {
                        // Handle date fields - format if not empty, keep empty if null
                        if (!empty($val)) {
                            try {
                                $ts = strtotime($val);
                                if ($ts !== false && $ts !== -1) {
                                    $val = date('d/M/Y', $ts);
                                }
                            } catch (\Throwable $e) {
                                // Keep original value if parsing fails
                            }
                        } else {
                            // Keep empty for null dates
                            $val = '';
                        }
                    }

                    // For numeric fields, add invisible zero-width space to prevent scientific notation
                    // This forces Excel to treat as text without showing visible characters
                    if (in_array($col, ['nik', 'ktp', 'kk', 'no_hp', 'akun_bank', 'jkn', 'no_ketenagakerjaan']) && !empty($val)) {
                        $val = "\u{200B}" . $val; // Zero-width space
                    }

                    // Escape fields that contain semicolons, quotes, or line breaks
                    if (strpos($val, ";") !== false || strpos($val, '"') !== false || strpos($val, "\n") !== false || strpos($val, "\r") !== false) {
                        $val = '"' . str_replace('"', '""', $val) . '"';
                    }

                    $line[] = $val;
                }
                fwrite($out, implode(";", $line) . "\r\n");
            }
        });

        fclose($out);
    };

    // Set headers and generate file
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    $callback();
    
    echo "File generated successfully!\n";
    
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

?>