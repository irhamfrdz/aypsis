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
        $this->createPranotaSchema();
        $details = [];
        foreach (range(1, 264) as $id) {
            $details[] = [
                'snapshot_karyawan' => ['id' => $id, 'unique_id' => 'Karyawan_'.$id, 'tipe_karyawan' => \App\Models\Karyawan::class,
                    'nama_lengkap' => 'Nama Saat Simpan', 'nik' => 'NIK-LAMA', 'group_jkn' => 'JKN-KIS',
                    'group_bp_jamsostek' => 'PPU', 'tipe_jkn' => 'KANTOR', 'tipe_bp_jamsostek' => 'KARYAWAN',
                    'dpp_jkn' => 5729880, 'dpp_bp_jamsostek' => 4450000.50],
                'ringkasan_bpjs' => ['ppu' => 12345.67, 'bpu' => 0, 'jkn' => 200000.50, 'kary_jht' => 123.45,
                    'kary_jkn' => 0, 'pt' => 234.56, 'non_kary' => 0],
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
            'snapshot_rumus_json' => json_encode([['jenis' => 'jkn', 'group_name' => 'JKN-KIS', 'nilai' => 4]]),
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
            $expected = array_sum(array_diff_key($details[0], array_flip(['snapshot_karyawan', 'ringkasan_bpjs', 'karyawan_id', 'tipe_karyawan', 'allowance_ppu', 'allowance_bpu', 'allowance_jkn', 'no_voucher', 'allowance_kary_jht', 'allowance_kary_jkn', 'allowance_pt', 'allowance_non_kary']))) * 264;
            $this->assertEqualsWithDelta($expected, (float) $header->grand_total, 0.01);
            $this->assertEqualsWithDelta($expected, $header->details()->sum('total'), 0.01);
            $header->load('details');
            foreach ($header->details as $savedDetail) {
                $savedDetail->setRelation('karyawan', new \App\Models\Karyawan([
                    'nama_lengkap' => 'Master Sudah Berubah', 'group_jkn' => 'JKN-KIS',
                    'group_bp_jamsostek' => 'PPU', 'tipe_jkn' => 'KANTOR',
                    'tipe_bp_jamsostek' => 'KARYAWAN', 'dpp_jkn' => 5729880, 'dpp_bp_jamsostek' => 4450000.50,
                ]));
            }
            $source = file_get_contents(resource_path('views/pranota-bpjs/show.blade.php'));
            $start = strpos($source, '        @php');
            $end = strpos($source, '        <div class="flex justify-end mt-16', $start);
            $template = substr($source, $start, $end - $start);
            $html = \Illuminate\Support\Facades\Blade::render($template, ['pranota_bpj' => $header]);
            $this->assertStringContainsString('Nama Saat Simpan', $html);
            $this->assertStringNotContainsString('Master Sudah Berubah', $html);
            $this->assertEquals(4, $header->snapshot_rumus[0]['nilai']);
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

    public function test_zero_iuran_and_manual_allowances_are_saved_with_frozen_employee_and_formula(): void
    {
        $this->createPranotaSchema();
        $detail = [
            'karyawan_id' => 1, 'tipe_karyawan' => \App\Models\Karyawan::class,
            'bpjs_kesehatan' => 0, 'no_voucher' => 'ZERO-001', 'allowance_pt' => 12500.75,
            'snapshot_karyawan' => ['id' => 1, 'unique_id' => 'Karyawan_1', 'nama_lengkap' => 'Nama Lama',
                'dpp_jkn' => 5500000, 'group_jkn' => 'JKN-KIS', 'is_special_case' => true],
            'ringkasan_bpjs' => ['ppu' => 0, 'bpu' => 0, 'jkn' => 0, 'kary_jht' => 0, 'kary_jkn' => 0,
                'pt' => 12500.75, 'non_kary' => 0],
        ];
        $payload = ['tanggal_pranota' => '2026-10-30', 'periode_bulan' => 10, 'periode_tahun' => 2026,
            'snapshot_rumus_json' => json_encode([['jenis' => 'jkn', 'nilai' => 4, 'diskon_nilai' => 25]]),
            'details_json' => json_encode([$detail])];
        $controller = new PranotaBpjsController;
        $controller->store(Request::create('/', 'POST', $payload));
        $header = PranotaBpjsHeader::firstOrFail();
        $saved = $header->details()->firstOrFail();
        $this->assertEquals(1, $header->total_karyawan);
        $this->assertSame('0.00', $saved->total);
        $this->assertSame('ZERO-001', $saved->no_voucher);
        $this->assertEquals(12500.75, $saved->summaryBurden('pt'));
        $this->assertSame('Nama Lama', $saved->karyawan_pranota->nama_lengkap);
        $this->assertEquals(5500000, $saved->karyawan_pranota->dpp_jkn);
        $this->assertEquals(25, $header->snapshot_rumus[0]['diskon_nilai']);
        $saved->setRelation('karyawan', new \App\Models\Karyawan([
            'nama_lengkap' => 'Nama Baru', 'dpp_jkn' => 999, 'is_special_case' => false,
        ]));
        $this->assertSame('Nama Lama', $saved->karyawan_pranota->nama_lengkap);
        $this->assertEquals(12500.75, $saved->summaryBurden('pt'));
        $payload['snapshot_rumus_json'] = json_encode([['nilai' => 99]]);
        $detail['allowance_pt'] = 333.25;
        $detail['ringkasan_bpjs']['pt'] = 333.25;
        $detail['tipe_jkn'] = 'manual';
        $payload['details_json'] = json_encode([$detail]);
        $controller->update(Request::create('/', 'PUT', $payload), $header);
        $header->refresh();
        $saved = $header->details()->firstOrFail();
        $this->assertEquals(4, $header->snapshot_rumus[0]['nilai']);
        $this->assertEquals(333.25, $saved->summaryBurden('pt'));
        $this->assertSame('manual', $saved->snapshot_bpjs['input']['tipe_jkn']);
        foreach (['karyawans', 'karyawan_tidak_tetaps'] as $tableName) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                foreach (['nik', 'nama_lengkap', 'group_jkn', 'group_bp_jamsostek', 'tipe_jkn', 'tipe_bp_jamsostek',
                    'dpp_jkn', 'dpp_bp_jamsostek', 'cabang_bpjs', 'tanggal_berhenti', 'tanggal_lahir'] as $column) {
                    $table->string($column)->nullable();
                }
                $table->boolean('is_special_case')->default(false);
            });
        }
        DB::table('karyawans')->insert(['id' => 1, 'nama_lengkap' => 'Master Terbaru', 'dpp_jkn' => 999]);
        DB::table('karyawan_tidak_tetaps')->insert(['id' => 1, 'nama_lengkap' => 'Tidak Tetap Lain']);
        foreach (['changed', 'deleted'] as $state) {
            if ($state === 'deleted') {
                DB::table('karyawans')->delete();
            }
            $edit = $controller->edit($header)->getData();
            $employee = $edit['karyawans']->firstWhere('unique_id', 'Karyawan_1');
            $this->assertSame('Nama Lama', $employee['nama_lengkap']);
            $this->assertEquals(5500000, $employee['dpp_jkn']);
            $this->assertNotNull($edit['karyawans']->firstWhere('unique_id', 'KaryawanTidakTetap_1'));
            $this->assertEquals(4, $edit['rumusBpjs'][0]['nilai']);
        }

        $this->assertSame('Nama Lama', $saved->karyawan_pranota->nama_lengkap);
    }

    private function createPranotaSchema(): void
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
        (require database_path('migrations/2026_10_30_100000_add_snapshots_to_pranota_bpjs_tables.php'))->up();
    }
}
