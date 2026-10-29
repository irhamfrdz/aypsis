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
        (require database_path('migrations/2026_10_28_160000_add_rincian_bpjs_to_pranota_bpjs_details_table.php'))->up();
        (require database_path('migrations/2026_10_28_170000_add_tunjangan_manual_to_pranota_bpjs_details_table.php'))->up();
        (require database_path('migrations/2026_10_29_100000_add_no_voucher_to_pranota_bpjs_details_table.php'))->up();
        $details = [];
        foreach (range(1, 264) as $id) {
            $details[] = [
                'karyawan_id' => $id, 'tipe_karyawan' => 'App\\Models\\Karyawan',
                'bpjs_kesehatan' => 229195.20, 'bpjs_ketenagakerjaan' => 57298.80,
                'jht_biaya' => 100000.80, 'jht_hutang' => 20000,
                'jkk_tunjangan' => 1000, 'jkm_tunjangan' => 2000,
                'jkk_hutang' => 3000, 'bpu_jkk_tunjangan' => 4000, 'bpu_jkm' => 5000,
                'noncrew_jht_biaya' => 6000, 'noncrew_jht_hutang' => 7000,
                'noncrew_jkk_tunjangan' => 8000, 'noncrew_jkm_tunjangan' => 9000,
                'jp_biaya' => 10000, 'jp_hutang' => 11000,
                'no_voucher' => 'VC-001/2026', 'allowance_kary_jht' => 123.45, 'allowance_kary_jkn' => 0, 'allowance_pt' => 234.56, 'allowance_non_kary' => 0,
                'allowance_ppu' => 12345.67, 'allowance_bpu' => 0, 'allowance_jkn' => 200000.50,
            ];
        }
        $data = ['tanggal_pranota' => '2026-10-28', 'periode_bulan' => 10, 'periode_tahun' => 2026,
            'details_json' => json_encode($details)];
        $controller = new PranotaBpjsController;
        DB::enableQueryLog();
        $controller->store(Request::create('/', 'POST', $data));
        $header = PranotaBpjsHeader::firstOrFail();
        foreach (['create', 'update'] as $action) {
            if ($action === 'update') {
                DB::flushQueryLog();
                $controller->update(Request::create('/', 'PUT', $data), $header);
                $header->refresh();
            }
            $insertCount = collect(DB::getQueryLog())->filter(fn ($entry) => preg_match('/^insert into ["`]?pranota_bpjs_details/i', $entry['query']))->count();
            $this->assertSame(7, $insertCount, '264 detail rows should use seven batch inserts on '.$action);
            $this->assertEquals(264, $header->total_karyawan);
            $this->assertSame(264, $header->details()->count());
            $this->assertSame('229195.20', $header->details()->firstOrFail()->bpjs_kesehatan);
            $this->assertSame('57298.80', $header->details()->firstOrFail()->bpjs_ketenagakerjaan);
            $this->assertSame('VC-001/2026', $header->details()->firstOrFail()->no_voucher);
            $this->assertSame(123.45, $header->details()->firstOrFail()->tunjangan_manual['kary_jht']);
            $this->assertSame(0, $header->details()->firstOrFail()->tunjangan_manual['kary_jkn']);
            $breakdown = $header->details()->firstOrFail()->rincian_bpjs;
            $this->assertSame(12345.67, $header->details()->firstOrFail()->tunjangan_manual['ppu']);
            $this->assertSame(0, $header->details()->firstOrFail()->tunjangan_manual['bpu']);
            $this->assertSame(4000.0, (float) $breakdown['bpu_jkk_tunjangan']);
            $this->assertSame(8000.0, (float) $breakdown['noncrew_jkk_tunjangan']);
            $this->assertSame(100000.8, (float) $breakdown['jht_biaya']);
            $expected = array_sum(array_diff_key($details[0], array_flip(['karyawan_id', 'tipe_karyawan', 'allowance_ppu', 'allowance_bpu', 'allowance_jkn', 'no_voucher', 'allowance_kary_jht', 'allowance_kary_jkn', 'allowance_pt', 'allowance_non_kary']))) * 264;
            $this->assertEqualsWithDelta($expected, (float) $header->grand_total, 0.01);
            $this->assertEqualsWithDelta($expected, $header->details()->sum('total'), 0.01);
            $header->load('details');
            foreach ($header->details as $savedDetail) {
                $savedDetail->setRelation('karyawan', new \App\Models\Karyawan([
                    'nama_lengkap' => 'Karyawan Uji', 'group_jkn' => 'JKN-KIS',
                    'group_bp_jamsostek' => 'PPU', 'tipe_jkn' => 'KANTOR',
                    'tipe_bp_jamsostek' => 'KARYAWAN', 'dpp_jkn' => 5729880, 'dpp_bp_jamsostek' => 4450000.50,
                ]));
            }
            $source = file_get_contents(resource_path('views/pranota-bpjs/show.blade.php'));
            $start = strpos($source, '        @php');
            $end = strpos($source, '        <div class="flex justify-end mt-16', $start);
            $template = substr($source, $start, $end - $start);
            $html = \Illuminate\Support\Facades\Blade::render($template, ['pranota_bpj' => $header]);
            foreach (['PPU JHT 3.7%', 'BPU JHT (Kary)', 'JHT 2% (Hutang)', 'PPU JP 1%', '100.000,80', '12.345,67', '200.000,50', 'Beban KARY. JKN 1%', 'Beban NON KARY. JKN &amp; BPJS', 'VC-001/2026', '123,45', 'TOTAL JKN', 'TOTAL PPU', '286.494,00', '144.000,80', 'TOTAL BPU-CREW', 'TOTAL NON BPU-CREW', '12.000,00', '30.000,00', 'DPP JKN', 'DPP BP Jamsostek', '5.729.880,00', '4.450.000,50', 'BP Jamsostek: PPU', 'KARYAWAN'] as $label) {
                $this->assertStringContainsString($label, $html);
            }
            $header->details->first()->rincian_bpjs = null;
            $legacyHtml = \Illuminate\Support\Facades\Blade::render($template, ['pranota_bpj' => $header]);
            $this->assertStringContainsString('Jamsostek Gabungan', $legacyHtml);
            $this->assertStringContainsString('data lama tanpa rincian PPU/BPU terpisah', $legacyHtml);
        }
        DB::disableQueryLog();
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });
        DB::table('users')->insert(['id' => 1, 'name' => 'Pembuat Pranota Uji']);
        $header->update(['created_by' => 1]);
        $historyRequest = Request::create('/pranota-bpjs/riwayat', 'GET', [
            'search' => 'Pembuat Pranota Uji', 'bulan' => 10, 'tahun' => 2026, 'status' => 'draft',
        ]);
        $route = app('router')->getRoutes()->match($historyRequest);
        $historyRequest->setRouteResolver(fn () => $route);
        $history = $controller->index($historyRequest)->getData();
        $this->assertSame('pranota-bpjs.history', $route->getName());
        $this->assertTrue($history['isHistory']);
        $this->assertSame(1, $history['pranotas']->total());
        $this->assertEqualsWithDelta((float) $header->grand_total, (float) $history['totalNominal'], 0.01);
        $this->assertSame(1, $history['draftCount']);
        $historyRequest->merge(['status' => 'approved']);
        $this->assertSame(0, $controller->index($historyRequest)->getData()['pranotas']->total());
    }
}
