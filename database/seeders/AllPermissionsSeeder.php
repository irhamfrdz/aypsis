<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Route;
use App\Models\Permission;

class AllPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1) Explicit, known permission names used across the app
        $explicit = [
            ['name' => 'master-karyawan', 'description' => 'Akses Master Karyawan'],
            ['name' => 'master-user', 'description' => 'Akses Master User'],
            ['name' => 'master-kontainer', 'description' => 'Akses Master Kontainer'],
            ['name' => 'master-permohonan', 'description' => 'Akses Master Permohonan'],
            ['name' => 'permohonan-create', 'description' => 'Membuat Permohonan'],
            ['name' => 'permohonan-view', 'description' => 'Melihat Permohonan'],
            ['name' => 'permohonan-edit', 'description' => 'Mengedit Permohonan'],
            ['name' => 'permohonan-delete', 'description' => 'Menghapus Permohonan'],
            ['name' => 'master-tujuan', 'description' => 'Akses Master Tujuan'],
            ['name' => 'master-kegiatan', 'description' => 'Akses Master Kegiatan'],
            ['name' => 'master-permission', 'description' => 'Akses Master Izin'],
            ['name' => 'master-mobil', 'description' => 'Akses Master Mobil'],
            ['name' => 'master-pricelist-sewa-kontainer', 'description' => 'Akses Master Pricelist Sewa Kontainer'],
            ['name' => 'master-pranota-supir', 'description' => 'Akses Master Pranota Supir'],
            ['name' => 'master-pembayaran-pranota-supir', 'description' => 'Akses Pembayaran Pranota Supir'],
            ['name' => 'master-pranota-tagihan-kontainer', 'description' => 'Akses Master Pranota Tagihan Kontainer'],
        ];

        foreach ($explicit as $p) {
            Permission::firstOrCreate(['name' => $p['name']], ['description' => $p['description'] ?? null]);
        }

        // 2) Add permissions derived from named routes (keeps parity with PermissionsFromRoutesSeeder)
        try {
            $routes = collect(Route::getRoutes())->map(fn($r) => $r->getName())->filter()->unique();
        } catch (\Throwable $e) {
            // In some seeding contexts Route may not be available; skip route-based seeding then
            $routes = collect();
        }

        $ignoredPrefixes = ['ignition', 'telescope', 'debugbar', 'horizon'];
        foreach ($routes as $name) {
            if (!$name) continue;
            $skip = false;
            foreach ($ignoredPrefixes as $pre) {
                if (str_starts_with($name, $pre)) { $skip = true; break; }
            }
            if ($skip) continue;
            Permission::firstOrCreate(['name' => $name], ['description' => 'Izin untuk route: ' . $name]);
        }
    }
}
