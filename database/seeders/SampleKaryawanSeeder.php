<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Karyawan;
use Carbon\Carbon;

class SampleKaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat 1 data karyawan lengkap untuk testing
        Karyawan::create([
            'nik' => '3201234567890123',
            'nama_lengkap' => 'Ahmad Fauzi Rahman',
            'nama_panggilan' => 'Ahmad',
            'email' => 'ahmad.fauzi@ayp.co.id',
            'tanggal_lahir' => '1990-05-15',
            'tempat_lahir' => 'Jakarta',
            'jenis_kelamin' => 'L',
            'agama' => 'Islam',
            'status_perkawinan' => 'Menikah',
            'no_hp' => '081234567890',
            'ktp' => '3201234567890123',
            'kk' => '3201234567890124',

            // Informasi Perusahaan
            'divisi' => 'Operasional',
            'pekerjaan' => 'Supervisor',
            'tanggal_masuk' => '2020-01-15',
            'tanggal_berhenti' => null,
            'tanggal_masuk_sebelumnya' => null,
            'tanggal_berhenti_sebelumnya' => null,
            'nik_supervisor' => '3201234567890100',
            'supervisor' => 'Budi Santoso',
            'cabang' => 'Jakarta',
            'plat' => 'B 1234 XYZ',

            // Informasi Alamat
            'alamat' => 'Jl. Sudirman No. 123',
            'rt_rw' => '001/002',
            'kelurahan' => 'Karet Tengsin',
            'kecamatan' => 'Tanah Abang',
            'kabupaten' => 'Jakarta Pusat',
            'provinsi' => 'DKI Jakarta',
            'kode_pos' => '10220',
            'alamat_lengkap' => 'Jl. Sudirman No. 123, 001/002, Karet Tengsin, Tanah Abang, Jakarta Pusat, DKI Jakarta, 10220',

            // Catatan
            'catatan' => 'Karyawan teladan dengan dedikasi tinggi. Memiliki sertifikat K3 dan pengalaman supervisi 5 tahun.',

            // Informasi Bank
            'nama_bank' => 'Bank Central Asia (BCA)',
            'bank_cabang' => 'Cabang Sudirman',
            'akun_bank' => '1234567890',
            'atas_nama' => 'Ahmad Fauzi Rahman',

            // Informasi Pajak & JKN
            'status_pajak' => 'K1',
            'jkn' => '0001234567890',
            'no_ketenagakerjaan' => 'JHT1234567890',

            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $this->command->info('✅ Sample karyawan data berhasil ditambahkan!');
        $this->command->info('📋 Data karyawan: Ahmad Fauzi Rahman');
        $this->command->info('🆔 NIK: 3201234567890123');
        $this->command->info('📧 Email: ahmad.fauzi@ayp.co.id');
        $this->command->info('🏢 Divisi: Operasional - Supervisor');
    }
}
