<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== IMPORT DATA KARYAWAN ===\n";

try {
    $sqlFile = 'c:\folder_kerjaan\aypsis3.sql';
    
    if (!file_exists($sqlFile)) {
        die("File SQL tidak ditemukan: $sqlFile\n");
    }
    
    echo "Membaca file SQL...\n";
    $content = file_get_contents($sqlFile);
    
    // Extract data INSERT untuk tabel karyawans
    $pattern = '/INSERT INTO `karyawans` VALUES\s*(.*?);/s';
    preg_match_all($pattern, $content, $matches);
    
    if (empty($matches[1])) {
        echo "Tidak ada data INSERT untuk tabel karyawans ditemukan.\n";
        echo "Mencoba pattern alternatif...\n";
        
        // Pattern alternatif
        $pattern2 = '/INSERT INTO `karyawans`.*?VALUES\s*(.*?);/s';
        preg_match_all($pattern2, $content, $matches2);
        
        if (empty($matches2[1])) {
            echo "Masih tidak ditemukan. Mari cari secara manual...\n";
            
            // Cari baris yang mengandung INSERT INTO karyawans
            $lines = explode("\n", $content);
            $insertLines = [];
            $collecting = false;
            
            foreach ($lines as $line) {
                if (strpos($line, 'INSERT INTO `karyawans`') !== false) {
                    $collecting = true;
                    $insertLines[] = $line;
                } elseif ($collecting) {
                    $insertLines[] = $line;
                    if (strpos($line, ';') !== false) {
                        break;
                    }
                }
            }
            
            if (!empty($insertLines)) {
                $insertStatement = implode("\n", $insertLines);
                echo "Ditemukan INSERT statement:\n";
                echo substr($insertStatement, 0, 500) . "...\n";
                
                // Parse VALUES
                if (preg_match('/VALUES\s*(.*?);/s', $insertStatement, $valueMatch)) {
                    $valuesString = $valueMatch[1];
                } else {
                    die("Tidak dapat menemukan VALUES dalam INSERT statement.\n");
                }
            } else {
                die("Tidak dapat menemukan INSERT statement untuk karyawans.\n");
            }
        } else {
            $valuesString = $matches2[1][0];
        }
    } else {
        $valuesString = $matches[1][0];
    }
    
    echo "Memproses data karyawan...\n";
    
    // Parse individual rows - contoh: (1,'John','Doe',...)
    $pattern = '/\(([^)]+(?:\([^)]*\)[^)]*)*)\)/';
    preg_match_all($pattern, $valuesString, $rowMatches);
    
    $insertedCount = 0;
    
    foreach ($rowMatches[1] as $rowData) {
        // Split values by comma, but handle quoted strings properly
        $values = [];
        $current = '';
        $inQuotes = false;
        $quoteChar = '';
        
        for ($i = 0; $i < strlen($rowData); $i++) {
            $char = $rowData[$i];
            
            if (!$inQuotes && ($char === "'" || $char === '"')) {
                $inQuotes = true;
                $quoteChar = $char;
                $current .= $char;
            } elseif ($inQuotes && $char === $quoteChar) {
                if ($i + 1 < strlen($rowData) && $rowData[$i + 1] === $quoteChar) {
                    // Escaped quote
                    $current .= $char . $char;
                    $i++; // Skip next char
                } else {
                    $inQuotes = false;
                    $current .= $char;
                }
            } elseif (!$inQuotes && $char === ',') {
                $values[] = trim($current);
                $current = '';
            } else {
                $current .= $char;
            }
        }
        
        if ($current !== '') {
            $values[] = trim($current);
        }
        
        if (count($values) < 10) {
            echo "Warning: Row dengan data tidak lengkap, dilewati.\n";
            continue;
        }
        
        // Clean values
        $cleanValues = [];
        foreach ($values as $value) {
            $value = trim($value);
            if ($value === 'NULL') {
                $cleanValues[] = null;
            } elseif (preg_match('/^\'(.*)\'$/', $value, $match)) {
                $cleanValues[] = str_replace("''", "'", $match[1]);
            } elseif (preg_match('/^"(.*)"$/', $value, $match)) {
                $cleanValues[] = str_replace('""', '"', $match[1]);
            } else {
                $cleanValues[] = $value;
            }
        }
        
        try {
            // Insert ke database dengan field mapping
            DB::table('karyawans')->insert([
                'id' => $cleanValues[0] ?? null,
                'nik' => $cleanValues[1] ?? null,
                'nama_lengkap' => $cleanValues[2] ?? null,
                'tempat_lahir' => $cleanValues[3] ?? null,
                'tanggal_lahir' => $cleanValues[4] ?? null,
                'jenis_kelamin' => $cleanValues[5] ?? null,
                'status_pernikahan' => $cleanValues[6] ?? null,
                'agama' => $cleanValues[7] ?? null,
                'pendidikan_terakhir' => $cleanValues[8] ?? null,
                'alamat' => $cleanValues[9] ?? null,
                'no_telepon' => $cleanValues[10] ?? null,
                'email' => $cleanValues[11] ?? null,
                'tanggal_masuk_kerja' => $cleanValues[12] ?? null,
                'posisi' => $cleanValues[13] ?? null,
                'divisi_id' => $cleanValues[14] ?? null,
                'gaji_pokok' => $cleanValues[15] ?? null,
                'status_karyawan' => $cleanValues[16] ?? null,
                'foto' => $cleanValues[17] ?? null,
                'created_at' => $cleanValues[18] ?? now(),
                'updated_at' => $cleanValues[19] ?? now(),
            ]);
            
            $insertedCount++;
            
            if ($insertedCount % 10 == 0) {
                echo "Imported $insertedCount records...\n";
            }
            
        } catch (Exception $e) {
            echo "Error inserting record: " . $e->getMessage() . "\n";
            echo "Data: " . print_r($cleanValues, true) . "\n";
        }
    }
    
    echo "\n=== SELESAI ===\n";
    echo "Total karyawan berhasil diimpor: $insertedCount\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}