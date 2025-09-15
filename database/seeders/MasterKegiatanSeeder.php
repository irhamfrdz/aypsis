<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterKegiatanSeeder extends Seeder
{
    public function run()
    {
        $now = now();

        $rows = [
            ['kode_kegiatan' => 'TKSEWA', 'nama_kegiatan' => 'Tarik Kontainer Sewa', 'keterangan' => 'Tarik/pengambilan kontainer sewa', 'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['kode_kegiatan' => 'PENGIRIMAN', 'nama_kegiatan' => 'Pengiriman', 'keterangan' => 'Pengiriman kontainer', 'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['kode_kegiatan' => 'PENGAMBILAN', 'nama_kegiatan' => 'Pengambilan', 'keterangan' => 'Pengambilan kontainer', 'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['kode_kegiatan' => 'PERBAIKAN', 'nama_kegiatan' => 'Perbaikan Kontainer', 'keterangan' => 'Perbaikan dan maintenance kontainer', 'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('master_kegiatans')->insertOrIgnore($rows);
    }
}
