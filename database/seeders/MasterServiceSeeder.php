<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MasterService;

class MasterServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'kode_service' => 'SVC001',
                'nama_service' => 'Loading/Unloading',
                'tarif' => 500000,
                'status' => 'aktif',
                'deskripsi' => 'Layanan bongkar muat kontainer'
            ],
            [
                'kode_service' => 'SVC002',
                'nama_service' => 'Storage',
                'tarif' => 200000,
                'status' => 'aktif',
                'deskripsi' => 'Layanan penyimpanan kontainer'
            ],
            [
                'kode_service' => 'SVC003',
                'nama_service' => 'Stuffing/Stripping',
                'tarif' => 750000,
                'status' => 'aktif',
                'deskripsi' => 'Layanan isi/kosongkan kontainer'
            ],
            [
                'kode_service' => 'SVC004',
                'nama_service' => 'Container Inspection',
                'tarif' => 300000,
                'status' => 'aktif',
                'deskripsi' => 'Layanan inspeksi kontainer'
            ],
            [
                'kode_service' => 'SVC005',
                'nama_service' => 'Repair & Maintenance',
                'tarif' => 1000000,
                'status' => 'aktif',
                'deskripsi' => 'Layanan perbaikan dan perawatan kontainer'
            ],
            [
                'kode_service' => 'SVC006',
                'nama_service' => 'Washing & Cleaning',
                'tarif' => 150000,
                'status' => 'aktif',
                'deskripsi' => 'Layanan pencucian dan pembersihan kontainer'
            ]
        ];

        foreach ($services as $service) {
            MasterService::create($service);
        }
    }
}
