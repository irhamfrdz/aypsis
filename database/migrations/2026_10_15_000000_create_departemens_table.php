<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departemens', function (Blueprint $table) {
            $table->id();
            $table->string('nama_departemen')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Preserve department names already used by employees.
        if (Schema::hasTable('karyawans') && Schema::hasColumn('karyawans', 'departemen')) {
            DB::table('karyawans')->whereNotNull('departemen')->where('departemen', '!=', '')
                ->distinct()->pluck('departemen')->each(function ($name) {
                    $name = trim($name);
                    if ($name !== '') {
                        DB::table('departemens')->updateOrInsert(
                            ['nama_departemen' => $name],
                            ['is_active' => true, 'created_at' => now(), 'updated_at' => now()]
                        );
                    }
                });
        }
        if (Schema::hasTable('jam_kerjas') && Schema::hasColumn('jam_kerjas', 'departemen')) {
            DB::table('jam_kerjas')->whereNotNull('departemen')->where('departemen', '!=', '')
                ->distinct()->pluck('departemen')->each(function ($name) {
                    $name = trim($name);
                    if ($name !== '') {
                        DB::table('departemens')->updateOrInsert(
                            ['nama_departemen' => $name],
                            ['is_active' => true, 'created_at' => now(), 'updated_at' => now()]
                        );
                    }
                });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('departemens');
    }
};
