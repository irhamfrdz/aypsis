<?php

namespace Tests\Feature;

use App\Http\Controllers\KaryawanTidakTetapController;
use App\Models\KaryawanTidakTetap;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class KaryawanTidakTetapBpjsTest extends KaryawanJamsostekOptionsTest
{
    protected function setUp(): void
    {
        parent::setUp();
        Schema::create('karyawan_tidak_tetaps', function (Blueprint $table) {
            $table->id();
            $table->string('nik')->unique();
            $table->string('nama_lengkap');
            $table->string('group_jkn')->nullable();
            $table->string('group_bp_jamsostek')->nullable();
            $table->timestamps();
        });
        $migration = require database_path('migrations/2026_10_28_100000_add_bpjs_types_to_karyawan_tidak_tetaps_table.php');
        $migration->up();
    }

    public function test_bpjs_types_are_saved_on_create_and_update(): void
    {
        $controller = new KaryawanTidakTetapController;
        $data = [
            'nik' => 'P0001', 'nama_lengkap' => 'Karyawan Uji',
            'group_jkn' => 'JKN-KIS', 'tipe_jkn' => 'KARYAWAN',
            'group_bp_jamsostek' => ' bpu-alexindo  pluit ', 'tipe_bp_jamsostek' => 'CREW',
        ];
        $controller->store(Request::create('/', 'POST', $data));
        $employee = KaryawanTidakTetap::firstOrFail();
        $this->assertSame('KARYAWAN', $employee->tipe_jkn);
        $this->assertSame('CREW', $employee->tipe_bp_jamsostek);
        $this->assertSame('BPU-ALEXINDO PLUIT', $employee->group_bp_jamsostek);

        $data['group_bp_jamsostek'] = 'PPU-KANTOR';
        $data['tipe_bp_jamsostek'] = 'KANTOR';
        $data['tipe_jkn'] = null;
        $controller->update(Request::create('/', 'PUT', $data), $employee);
        $this->assertSame('KANTOR', $employee->fresh()->tipe_bp_jamsostek);
        $this->assertNull($employee->fresh()->tipe_jkn);
    }

    public function test_create_rejects_a_type_from_another_group(): void
    {
        $this->expectException(ValidationException::class);
        (new KaryawanTidakTetapController)->store(Request::create('/', 'POST', [
            'nik' => 'P0001', 'nama_lengkap' => 'Karyawan Uji',
            'group_bp_jamsostek' => 'PPU-KANTOR', 'tipe_bp_jamsostek' => 'CREW',
        ]));
    }
}
