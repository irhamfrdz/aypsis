<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class PembayaranPranotaUangJalanBongkaranPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'pembayaran-pranota-uang-jalan-bongkaran-view', 'description' => 'Melihat pembayaran pranota uang jalan bongkaran'],
            ['name' => 'pembayaran-pranota-uang-jalan-bongkaran-create', 'description' => 'Membuat pembayaran pranota uang jalan bongkaran'],
            ['name' => 'pembayaran-pranota-uang-jalan-bongkaran-edit', 'description' => 'Mengubah pembayaran pranota uang jalan bongkaran'],
            ['name' => 'pembayaran-pranota-uang-jalan-bongkaran-delete', 'description' => 'Menghapus pembayaran pranota uang jalan bongkaran'],
            ['name' => 'pembayaran-pranota-uang-jalan-bongkaran-approve', 'description' => 'Menyetujui pembayaran pranota uang jalan bongkaran'],
            ['name' => 'pembayaran-pranota-uang-jalan-bongkaran-print', 'description' => 'Mencetak pembayaran pranota uang jalan bongkaran'],
            ['name' => 'pembayaran-pranota-uang-jalan-bongkaran-export', 'description' => 'Mengexport pembayaran pranota uang jalan bongkaran']
        ];

        DB::transaction(function () use ($permissions) {
            foreach ($permissions as $p) {
                $permission = Permission::where('name', $p['name'])->first();
                if (!$permission) {
                    Permission::create(['name' => $p['name'], 'description' => $p['description'], 'created_at' => now(), 'updated_at' => now()]);
                    $this->command->info("Created permission: {$p['name']}");
                } else {
                    $this->command->warn("Permission already exists: {$p['name']}");
                }
            }
        });

        $this->command->info('Finished creating pembayaran-pranota-uang-jalan-bongkaran permissions');
    }
}
