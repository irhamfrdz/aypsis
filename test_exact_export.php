<?php

require_once 'vendor/autoload.php';

use App\Models\Karyawan;

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TESTING EXACT EXPORT LOGIC ===\n";

try {
    // Clear any potential cache to ensure fresh data
    if (function_exists('opcache_reset')) {
        opcache_reset();
    }
    
    $columns = [
        'nik','nama_panggilan','nama_lengkap','plat','email','ktp','kk','alamat','rt_rw','kelurahan','kecamatan','kabupaten','provinsi','kode_pos','alamat_lengkap','tempat_lahir','tanggal_lahir','no_hp','jenis_kelamin','status_perkawinan','agama','divisi','pekerjaan','tanggal_masuk','tanggal_berhenti','tanggal_masuk_sebelumnya','tanggal_berhenti_sebelumnya','catatan','status_pajak','nama_bank','bank_cabang','akun_bank','atas_nama','jkn','no_ketenagakerjaan','cabang','nik_supervisor','supervisor'
    ];
    
    echo "Generating CSV data for all karyawan...\n";
    
    $count = 0;
    $rohmanFound = false;
    
    Karyawan::chunk(200, function($rows) use ($columns, &$count, &$rohmanFound) {
        foreach ($rows as $r) {
            // Get fresh instance to ensure we have the latest data
            $r = $r->fresh();
            
            $count++;
            
            // Check if this is Rohman
            if (stripos($r->nama_lengkap, 'rohman') !== false) {
                $rohmanFound = true;
                echo "\n=== FOUND ROHMAN (Row $count) ===\n";
                echo "ID: " . $r->id . "\n";
                echo "Raw tanggal_berhenti: " . ($r->tanggal_berhenti ?? 'NULL') . "\n";
                echo "Type: " . gettype($r->tanggal_berhenti) . "\n";
                
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

                    $line[] = $val;
                }
                
                echo "CSV Line: " . implode(";", $line) . "\n";
                
                // Check specifically tanggal_berhenti column
                $tanggalBerhentiIndex = array_search('tanggal_berhenti', $columns);
                echo "Tanggal berhenti in CSV (index $tanggalBerhentiIndex): '" . $line[$tanggalBerhentiIndex] . "'\n";
            }
        }
    });
    
    echo "\nTotal processed: $count\n";
    echo "Rohman found: " . ($rohmanFound ? "YES" : "NO") . "\n";
    
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

?>