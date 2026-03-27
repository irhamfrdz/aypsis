<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Permission;

class PembayaranPranotaUangJalanBatamPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'pembayaran-pranota-uang-jalan-batam-view', 'description' => 'Melihat pembayaran pranota uang jalan batam'],
            ['name' => 'pembayaran-pranota-uang-jalan-batam-create', 'description' => 'Membuat pembayaran pranota uang jalan batam'],
            ['name' => 'pembayaran-pranota-uang-jalan-batam-edit', 'description' => 'Mengubah pembayaran pranota uang jalan batam'],
            ['name' => 'pembayaran-pranota-uang-jalan-batam-delete', 'description' => 'Menghapus pembayaran pranota uang jalan batam'],
            ['name' => 'pembayaran-pranota-uang-jalan-batam-approve', 'description' => 'Menyetujui pembayaran pranota uang jalan batam'],
            ['name' => 'pembayaran-pranota-uang-jalan-batam-print', 'description' => 'Mencetak pembayaran pranota uang jalan batam'],
            ['name' => 'pembayaran-pranota-uang-jalan-batam-export', 'description' => 'Mengexport pembayaran pranota uang jalan batam']
        ];

        DB::transaction(function () use ($permissions) {
            foreach ($permissions as $p) {
                $permission = Permission::where('name', $p['name'])->first();
                if (!$permission) {
                    Permission::create([
                        'name' => $p['name'], 
                        'description' => $p['description'], 
                        'created_at' => now(), 
                        'updated_at' => now()
                    ]);
                    $this->command->info("Created permission: {$p['name']}");
                } else {
                    $this->command->warn("Permission already exists: {$p['name']}");
                }
            }
        });

        $this->command->info('Finished creating pembayaran-pranota-uang-jalan-batam permissions');
    }
}
