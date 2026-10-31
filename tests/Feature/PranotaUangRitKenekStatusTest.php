<?php

namespace Tests\Feature;

use App\Http\Controllers\PranotaUangRitKenekController;
use App\Models\PranotaUangRitKenek;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PranotaUangRitKenekStatusTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'cache.default' => 'array']);
        DB::purge('sqlite');
        Auth::shouldReceive('id')->andReturn(1);
        Auth::shouldReceive('user')->andReturn((object) ['name' => 'Tester']);

        foreach (['surat_jalans', 'surat_jalan_bongkarans'] as $table) {
            Schema::create($table, function (Blueprint $t) {
                $t->id();
                $t->string('no_surat_jalan')->nullable();
                $t->string('nomor_surat_jalan')->nullable();
                $t->string('status_pembayaran_uang_rit')->nullable();
                $t->string('status_pembayaran_uang_rit_kenek')->nullable();
                $t->timestamps();
            });
        }
        Schema::create('karyawans', function (Blueprint $t) {
            $t->id();
            $t->string('nama_lengkap');
            $t->string('nama_panggilan');
            $t->date('tanggal_berhenti')->nullable();
        });
        Schema::create('nomor_terakhir', function (Blueprint $t) {
            $t->id();
            $t->string('modul');
            $t->integer('nomor_terakhir');
            $t->string('keterangan');
            $t->timestamps();
        });
        Schema::create('pranota_uang_rit_keneks', function (Blueprint $t) {
            $t->id();
            foreach (['no_pranota', 'tanggal', 'no_surat_jalan', 'kenek_nama', 'no_plat', 'keterangan', 'status', 'tanggal_bayar', 'approved_at'] as $column) {
                $t->string($column)->nullable();
            }
            foreach (['surat_jalan_id', 'surat_jalan_bongkaran_id', 'created_by', 'updated_by', 'approved_by'] as $column) {
                $t->integer($column)->nullable();
            }
            foreach (['uang_jalan', 'uang_rit', 'uang_rit_kenek', 'total_rit', 'total_uang', 'total_hutang', 'total_tabungan', 'total_bpjs', 'grand_total_bersih'] as $column) {
                $t->decimal($column, 15, 2)->default(0);
            }
            $t->timestamps();
        });
        Schema::create('pranota_uang_rit_kenek_details', function (Blueprint $t) {
            $t->id();
            $t->string('no_pranota');
            $t->string('kenek_nama');
            foreach (['total_uang_kenek', 'hutang', 'tabungan', 'bpjs', 'grand_total'] as $column) {
                $t->decimal($column, 15, 2)->default(0);
            }
            $t->timestamps();
        });
        DB::table('karyawans')->insert(['nama_lengkap' => 'Kenek Test', 'nama_panggilan' => 'Kenek']);
        DB::table('surat_jalans')->insert([
            ['id' => 1, 'no_surat_jalan' => 'SJ-1', 'status_pembayaran_uang_rit' => 'belum_dibayar', 'status_pembayaran_uang_rit_kenek' => 'belum_dibayar'],
            ['id' => 2, 'no_surat_jalan' => 'SJ-2', 'status_pembayaran_uang_rit' => 'dibayar', 'status_pembayaran_uang_rit_kenek' => 'belum_dibayar'],
            ['id' => 3, 'no_surat_jalan' => 'UNSELECTED', 'status_pembayaran_uang_rit' => 'belum_dibayar', 'status_pembayaran_uang_rit_kenek' => 'belum_dibayar'],
        ]);
        DB::table('surat_jalan_bongkarans')->insert([
            ['id' => 1, 'nomor_surat_jalan' => 'SS-1', 'status_pembayaran_uang_rit' => null, 'status_pembayaran_uang_rit_kenek' => null],
            ['id' => 2, 'nomor_surat_jalan' => 'SS-2', 'status_pembayaran_uang_rit' => 'lunas', 'status_pembayaran_uang_rit_kenek' => null],
        ]);
    }

    private function createPranota(): PranotaUangRitKenek
    {
        $regular = $bongkaran = [];
        foreach ([1, 2] as $id) {
            $data = ['selected' => true, 'kenek_nama' => 'Kenek Test', 'uang_rit_kenek' => 50000];
            $regular[$id] = $data + ['no_surat_jalan' => 'SJ-'.$id];
            $bongkaran[$id] = $data + ['no_surat_jalan' => 'SS-'.$id];
        }
        $response = (new PranotaUangRitKenekController)->store(Request::create('/', 'POST', [
            'tanggal' => '2026-10-31', 'surat_jalan_data' => $regular, 'surat_jalan_bongkaran_data' => $bongkaran,
        ]));
        $this->assertTrue($response->getSession()->has('success'));

        return PranotaUangRitKenek::firstOrFail();
    }

    private function assertStatuses(string $regularKenek, string $bongkaranKenek): void
    {
        $this->assertSame(['belum_dibayar', 'dibayar', 'belum_dibayar'], DB::table('surat_jalans')->orderBy('id')->pluck('status_pembayaran_uang_rit')->all());
        $this->assertSame([null, 'lunas'], DB::table('surat_jalan_bongkarans')->orderBy('id')->pluck('status_pembayaran_uang_rit')->all());
        $this->assertSame([$regularKenek, $regularKenek, 'belum_dibayar'], DB::table('surat_jalans')->orderBy('id')->pluck('status_pembayaran_uang_rit_kenek')->all());
        $this->assertSame([$bongkaranKenek, $bongkaranKenek], DB::table('surat_jalan_bongkarans')->orderBy('id')->pluck('status_pembayaran_uang_rit_kenek')->all());
    }

    public function test_creation_updates_only_kenek_status_for_every_selected_surat_jalan(): void
    {
        Model::withoutEvents(function () {
            $this->createPranota();
            $this->assertStatuses('sudah_masuk_pranota', 'lunas');
        });
    }

    public function test_submit_approve_and_pay_do_not_change_driver_status(): void
    {
        Model::withoutEvents(function () {
            $pranota = $this->createPranota();
            $controller = new PranotaUangRitKenekController;
            $controller->submit($pranota);
            $this->assertSame('submitted', $pranota->fresh()->status);
            $this->assertStatuses('pranota_submitted', 'lunas');
            $controller->approve($pranota);
            $this->assertSame('approved', $pranota->fresh()->status);
            $this->assertStatuses('pranota_approved', 'lunas');
            $controller->markAsPaid(Request::create('/', 'POST', ['tanggal_bayar' => '2026-10-31']), $pranota);
            $this->assertSame('paid', $pranota->fresh()->status);
            $this->assertStatuses('dibayar', 'lunas');
        });
    }

    public function test_deleting_draft_releases_only_kenek_status(): void
    {
        Model::withoutEvents(function () {
            $pranota = $this->createPranota();
            (new PranotaUangRitKenekController)->destroy($pranota);
            $this->assertSame(0, PranotaUangRitKenek::count());
            $this->assertStatuses('belum_dibayar', 'belum_bayar');
        });
    }

    public function test_failed_status_sync_rolls_back_pranota_and_surat_jalan_together(): void
    {
        Model::withoutEvents(function () {
            $pranota = $this->createPranota();
            Schema::table('surat_jalan_bongkarans', fn (Blueprint $t) => $t->dropColumn('status_pembayaran_uang_rit_kenek'));
            $response = (new PranotaUangRitKenekController)->submit($pranota);
            $this->assertTrue($response->getSession()->has('error'));
            $this->assertSame('draft', $pranota->fresh()->status);
            $this->assertSame(['sudah_masuk_pranota', 'sudah_masuk_pranota', 'belum_dibayar'], DB::table('surat_jalans')->orderBy('id')->pluck('status_pembayaran_uang_rit_kenek')->all());
            $this->assertSame(['belum_dibayar', 'dibayar', 'belum_dibayar'], DB::table('surat_jalans')->orderBy('id')->pluck('status_pembayaran_uang_rit')->all());
        });
    }
}
