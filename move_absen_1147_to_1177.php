<?php

// Script untuk memindahkan data absensi dari NIK 1147 (Feriyanto) ke NIK 1177 (Elfani)

$nikAsal = '1147'; // Feriyanto
$nikTujuan = '1177'; // Elfani

$karyawanTujuan = \App\Models\Karyawan::where('nik', $nikTujuan)->first();

if ($karyawanTujuan) {
    // Pindahkan semua data absensi dari NIK 1147 ke 1177
    $jumlahDataDipindah = \App\Models\Absensi::where('nik', $nikAsal)
        ->update([
            'nik' => $karyawanTujuan->nik,
            'karyawan_id' => $karyawanTujuan->id
        ]);
    
    echo "Berhasil memindahkan $jumlahDataDipindah log absensi dari NIK $nikAsal (Feriyanto) ke NIK $nikTujuan (Elfani)!\n";
} else {
    echo "Gagal: Data Karyawan dengan NIK $nikTujuan (Elfani) tidak ditemukan di database.\n";
}
