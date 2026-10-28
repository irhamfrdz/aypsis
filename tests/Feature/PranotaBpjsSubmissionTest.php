<?php

namespace Tests\Feature;

use App\Http\Controllers\PranotaBpjsController;
use App\Models\PranotaBpjsHeader;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PranotaBpjsSubmissionTest extends TestCase
{
    public function test_large_pranota_preserves_every_employee_and_total_on_create_and_update(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('pranota_bpjs_headers', function (Blueprint $table) {
            $table->id();
            foreach (['nomor_pranota', 'tanggal_pranota', 'periode_bulan', 'periode_tahun', 'keterangan', 'status', 'created_by', 'updated_by'] as $column) {
                $table->string($column)->nullable();
            }
            foreach (['total_bpjs_kesehatan', 'total_bpjs_ketenagakerjaan', 'grand_total', 'total_karyawan'] as $column) {
                $table->decimal($column, 18, 2)->default(0);
            }
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('pranota_bpjs_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pranota_bpjs_header_id');
            $table->unsignedBigInteger('karyawan_id');
            $table->string('tipe_karyawan');
            foreach (['bpjs_kesehatan', 'bpjs_ketenagakerjaan', 'jht_biaya', 'jht_hutang', 'jkk_tunjangan', 'jkm_tunjangan', 'jp_biaya', 'jp_hutang', 'total'] as $column) {
                $table->decimal($column, 18, 2)->default(0);
            }
            $table->timestamps();
        });
        $details = [];
        foreach (range(1, 264) as $id) {
            $details[] = [
                'karyawan_id' => $id, 'tipe_karyawan' => 'App\\Models\\Karyawan',
                'bpjs_kesehatan' => 229195, 'bpjs_ketenagakerjaan' => 57299,
                'jht_biaya' => 100000.80, 'jht_hutang' => 20000,
                'jkk_tunjangan' => 1000, 'jkm_tunjangan' => 2000,
                'jkk_hutang' => 3000, 'bpu_jkk_tunjangan' => 4000, 'bpu_jkm' => 5000,
                'noncrew_jht_biaya' => 6000, 'noncrew_jht_hutang' => 7000,
                'noncrew_jkk_tunjangan' => 8000, 'noncrew_jkm_tunjangan' => 9000,
                'jp_biaya' => 10000, 'jp_hutang' => 11000,
            ];
        }
        $data = ['tanggal_pranota' => '2026-10-28', 'periode_bulan' => 10, 'periode_tahun' => 2026,
            'details_json' => json_encode($details)];
        $controller = new PranotaBpjsController;
        $controller->store(Request::create('/', 'POST', $data));
        $header = PranotaBpjsHeader::firstOrFail();
        foreach (['create', 'update'] as $action) {
            if ($action === 'update') {
                $controller->update(Request::create('/', 'PUT', $data), $header);
                $header->refresh();
            }
            $this->assertEquals(264, $header->total_karyawan);
            $this->assertSame(264, $header->details()->count());
            $expected = array_sum(array_diff_key($details[0], array_flip(['karyawan_id', 'tipe_karyawan']))) * 264;
            $this->assertEqualsWithDelta($expected, (float) $header->grand_total, 0.01);
            $this->assertEqualsWithDelta($expected, $header->details()->sum('total'), 0.01);
        }
    }
}
