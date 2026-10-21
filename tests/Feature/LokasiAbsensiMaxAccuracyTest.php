<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LokasiAbsensiMaxAccuracyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        (require database_path('migrations/2026_07_13_000000_create_lokasi_absensis_table.php'))->up();
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap');
            $table->string('nik');
            $table->string('divisi')->nullable();
        });
        (require database_path('migrations/2026_09_22_120000_create_lokasi_absensi_karyawan_tables.php'))->up();
        (require database_path('migrations/2026_10_21_000001_add_max_accuracy_to_lokasi_absensis_table.php'))->up();
    }

    public function test_accuracy_can_be_created_read_updated_preserved_and_cleared(): void
    {
        $payload = ['nama_lokasi' => 'Kantor', 'latitude' => -6.2, 'longitude' => 106.8];
        $id = $this->postJson('/api/lokasi-absensi', $payload + ['max_accuracy' => 50])
            ->assertOk()->json('id');
        $this->getJson('/api/lokasi-absensi')->assertOk()->assertJsonFragment(['max_accuracy' => 50]);
        $this->putJson('/api/lokasi-absensi/'.$id, $payload + ['max_accuracy' => 25])->assertOk();
        $this->assertDatabaseHas('lokasi_absensis', ['id' => $id, 'max_accuracy' => 25]);
        $this->putJson('/api/lokasi-absensi/'.$id, $payload)->assertOk();
        $this->assertDatabaseHas('lokasi_absensis', ['id' => $id, 'max_accuracy' => 25]);
        $this->putJson('/api/lokasi-absensi/'.$id, $payload + ['max_accuracy' => null])->assertOk();
        $this->assertDatabaseHas('lokasi_absensis', ['id' => $id, 'max_accuracy' => null]);
        $this->postJson('/api/lokasi-absensi', $payload)->assertOk();
    }

    public function test_invalid_accuracy_is_rejected_for_create_and_update(): void
    {
        $payload = ['nama_lokasi' => 'Kantor', 'latitude' => -6.2, 'longitude' => 106.8];
        $id = $this->postJson('/api/lokasi-absensi', $payload)->assertOk()->json('id');
        foreach ([0, -1, 1.5, 'invalid', 2147483648] as $value) {
            $this->postJson('/api/lokasi-absensi', $payload + ['max_accuracy' => $value])
                ->assertUnprocessable()->assertJsonValidationErrors('max_accuracy');
            $this->putJson('/api/lokasi-absensi/'.$id, $payload + ['max_accuracy' => $value])
                ->assertUnprocessable()->assertJsonValidationErrors('max_accuracy');
        }
    }
}
