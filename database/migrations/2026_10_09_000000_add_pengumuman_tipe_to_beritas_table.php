<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE beritas MODIFY tipe ENUM('berita', 'pamflet', 'pengumuman') NOT NULL DEFAULT 'berita'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::table('beritas')->where('tipe', 'pengumuman')->update(['tipe' => 'berita']);
            DB::statement("ALTER TABLE beritas MODIFY tipe ENUM('berita', 'pamflet') NOT NULL DEFAULT 'berita'");
        }
    }
};
