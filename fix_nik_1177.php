<?php

// 1. Perbarui Profil Karyawan
$dataKaryawan = [
    "user_id" => null,
    "nama_panggilan" => "ELFANI",
    "nama_lengkap" => "ELFANI SHARAULITA SIHOMBING",
    "plat" => null,
    "email" => "elfanisharaulita@gmail.com",
    "ktp" => "3274035311000006",
    "kk" => "3274030505070021",
    "alamat" => "JL. GN PANGRANGO 1 NO.80",
    "rt_rw" => "004/001",
    "kelurahan" => "LARANGAN",
    "kecamatan" => "HARJAMUKTI",
    "kabupaten" => "CIREBON",
    "provinsi" => "JAWA BARAT",
    "kode_pos" => "45141",
    "alamat_lengkap" => "JL. GN PANGRANGO 1 NO.80, 004/001, LARANGAN, HARJAMUKTI, CIREBON, JAWA BARAT, 45141",
    "tempat_lahir" => "CIREBON",
    "tanggal_lahir" => "2000-11-12",
    "no_hp" => "0895379370218",
    "jenis_kelamin" => "PEREMPUAN",
    "status_perkawinan" => null,
    "agama" => "KRISTEN",
    "divisi" => "ADMINISTRASI",
    "pekerjaan" => "PAJAK",
    "tanggal_masuk" => "2023-07-09",
    "tanggal_masuk_sebelumnya" => null,
    "tanggal_berhenti" => null,
    "tanggal_berhenti_sebelumnya" => null,
    "catatan" => null,
    "catatan_pekerjaan" => null,
    "status_pajak" => "TK/0",
    "nama_bank" => null,
    "bank_cabang" => null,
    "akun_bank" => null,
    "atas_nama" => null,
    "jkn" => "0002961309993",
    "status_jkn" => null,
    "no_ketenagakerjaan" => "23184714923",
    "status_bp_jamsostek" => null,
    "cabang_bpjs" => null,
    "no_sim" => null,
    "sim_berlaku_mulai" => null,
    "sim_berlaku_sampai" => null,
    "cabang" => "JAKARTA",
    "nik_supervisor" => null,
    "supervisor" => null,
    "status" => "active"
];

$karyawan = \App\Models\Karyawan::updateOrCreate(['nik' => '1177'], $dataKaryawan);
echo "1. Data Profil Karyawan dengan NIK 1177 berhasil diperbarui!\n";

// 2. Perbarui Data Absensi
$absensiData = [
    [
        "nik" => "1177",
        "waktu" => "2026-07-10 08:16:00",
        "tipe" => "Masuk",
        "mesin_id" => 1,
        "keterangan" => "Auto-sync database lokal MESIN KANTOR JAKARTA",
    ],
    [
        "nik" => "1177",
        "waktu" => "2026-07-10 18:01:52",
        "tipe" => "Pulang",
        "mesin_id" => 1,
        "keterangan" => "Auto-sync database lokal MESIN KANTOR JAKARTA",
    ],
    [
        "nik" => "1177",
        "waktu" => "2026-07-11 06:17:00",
        "tipe" => "Masuk",
        "mesin_id" => 1,
        "keterangan" => "Auto-sync database lokal MESIN KANTOR JAKARTA",
    ]
];

foreach ($absensiData as $absen) {
    $absen['karyawan_id'] = $karyawan->id;

    \App\Models\Absensi::updateOrCreate(
        [
            'nik' => $absen['nik'],
            'waktu' => $absen['waktu']
        ], 
        $absen
    );
}

echo "2. Data Absensi untuk NIK 1177 berhasil diperbarui!\n";
echo "Selesai.\n";
