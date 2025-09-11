<?php

// Simple test to check Kartu Keluarga status for karyawan ID 6
$karyawan = App\Models\Karyawan::find(6);
if ($karyawan) {
    echo 'Karyawan: ' . $karyawan->nama_lengkap . PHP_EOL;
    echo 'Divisi: ' . $karyawan->divisi . PHP_EOL;

    $kk = $karyawan->crewChecklists()->where('item_name', 'Kartu Keluarga')->first();
    if ($kk) {
        echo 'Kartu Keluarga - Status: ' . $kk->status . ', Nomor: ' . ($kk->nomor_sertifikat ?? 'null') . PHP_EOL;
    } else {
        echo 'Kartu Keluarga not found' . PHP_EOL;
    }
} else {
    echo 'Karyawan not found' . PHP_EOL;
}
