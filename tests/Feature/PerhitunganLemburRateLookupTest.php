<?php

namespace Tests\Feature;

use App\Http\Controllers\PerhitunganLemburController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PerhitunganLemburRateLookupTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');

        Schema::create('uang_makans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('karyawan_id');
            $table->date('tanggal');
            $table->decimal('nominal', 15, 2);
            $table->string('tipe_karyawan')->nullable();
        });
    }

    public function test_latest_rate_is_selected_per_employee_with_the_existing_tie_break_and_type_filter(): void
    {
        DB::table('uang_makans')->insert([
            ['id' => 1, 'karyawan_id' => 1, 'tanggal' => '2026-08-01', 'nominal' => 20000, 'tipe_karyawan' => null],
            ['id' => 2, 'karyawan_id' => 1, 'tanggal' => '2026-09-01', 'nominal' => 25000, 'tipe_karyawan' => null],
            ['id' => 3, 'karyawan_id' => 1, 'tanggal' => '2026-09-01', 'nominal' => 30000, 'tipe_karyawan' => 'Karyawan'],
            ['id' => 4, 'karyawan_id' => 1, 'tanggal' => '2026-10-01', 'nominal' => 90000, 'tipe_karyawan' => 'KaryawanTidakTetap'],
            ['id' => 5, 'karyawan_id' => 2, 'tanggal' => '2026-09-01', 'nominal' => 40000, 'tipe_karyawan' => null],
        ]);

        $method = new \ReflectionMethod(PerhitunganLemburController::class, 'latestUangMakanMap');
        $rates = $method->invoke(null, [1, 2]);

        $this->assertSame(30000.0, $rates->get(1));
        $this->assertSame(40000.0, $rates->get(2));
        $this->assertCount(2, $rates);
    }
}
