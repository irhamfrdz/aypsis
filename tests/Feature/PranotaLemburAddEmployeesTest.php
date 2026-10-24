<?php

namespace Tests\Feature;

use App\Models\PranotaLemburKaryawanHeader;
use App\Models\User;
use App\Services\PranotaLemburEmployeeService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PranotaLemburAddEmployeesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'cache.default' => 'array']);
        DB::purge('sqlite');
        Cache::flush();
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            foreach (['nik', 'nama_lengkap', 'grup'] as $column) $table->string($column);
            $table->integer('nominal_uang_makan')->default(0);
        });
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->integer('karyawan_id');
            $table->string('nik');
            $table->string('tipe');
            $table->dateTime('waktu');
            $table->date('tanggal_kerja_manual')->nullable();
        });
        Schema::create('uang_makans', function (Blueprint $table) {
            $table->id();
            $table->integer('karyawan_id');
            $table->integer('nominal');
            $table->date('tanggal');
            $table->string('tipe_karyawan')->nullable();
        });
        Schema::create('master_uang_lemburs', function (Blueprint $table) {
            $table->id();
            $table->string('group');
            $table->string('sub_group');
            $table->integer('pengali_uang_makan_hari_libur')->default(1);
        });
        Schema::create('uang_lembur_rules', function (Blueprint $table) {
            $table->id();
            $table->integer('uang_lembur_id');
            $table->string('tipe_hari');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->boolean('is_sampai_selesai')->default(false);
            $table->string('satuan');
            $table->integer('nominal');
        });
        Schema::create('hari_liburs', function (Blueprint $table) { $table->id(); $table->date('tanggal'); });
        Schema::create('pranota_lembur_karyawan_headers', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pranota');
            foreach (['tanggal_pranota', 'periode_mulai', 'periode_selesai'] as $column) $table->date($column);
            foreach (['total_biaya', 'adjustment', 'total_setelah_adjustment'] as $column) $table->decimal($column, 15, 2)->default(0);
            $table->integer('pranota_puml_id')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('pranota_lembur_karyawans', function (Blueprint $table) {
            $table->id();
            $table->integer('pranota_lembur_karyawan_header_id');
            $table->integer('karyawan_id');
            $table->date('periode_mulai')->nullable();
            $table->date('periode_selesai')->nullable();
            $table->json('tanggal_lembur')->nullable();
            $table->string('jam_lembur')->nullable();
            foreach (['nominal_awal', 'adjustment', 'total_akhir'] as $column) $table->decimal($column, 15, 2)->default(0);
            $table->string('catatan')->nullable();
            $table->timestamps();
        });
        Schema::create('pranota_pumls', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            foreach (['total_lembur', 'total_uang_makan', 'grand_total'] as $column) $table->decimal($column, 15, 2);
            $table->timestamps();
        });
        for ($id = 1; $id <= 4; $id++) {
            DB::table('karyawans')->insert(['id' => $id, 'nik' => (string) $id, 'nama_lengkap' => 'Karyawan '.$id, 'grup' => json_encode(['LEMBUR:PELABUHAN 1'])]);
        }
        DB::table('master_uang_lemburs')->insert(['id' => 1, 'group' => 'LEMBUR', 'sub_group' => 'PELABUHAN 1']);
        DB::table('uang_lembur_rules')->insert(['uang_lembur_id' => 1, 'tipe_hari' => 'Hari Biasa', 'jam_mulai' => '18:00:00', 'jam_selesai' => '08:00:00', 'satuan' => 'Jam', 'nominal' => 5000]);
        for ($id = 2; $id <= 3; $id++) {
            foreach (['2026-10-19', '2026-10-20'] as $date) {
                foreach (['lembur_masuk' => '19:00:00', 'lembur_pulang' => '22:00:00'] as $type => $time) {
                    DB::table('absensis')->insert(['karyawan_id' => $id, 'nik' => (string) $id, 'tipe' => $type, 'waktu' => $date.' '.$time]);
                }
            }
        }
        foreach ([1, 2, 3] as $id) {
            DB::table('pranota_lembur_karyawan_headers')->insert([
                'id' => $id, 'nomor_pranota' => 'PML-TEST-'.$id, 'tanggal_pranota' => '2026-10-24',
                'periode_mulai' => '2026-10-19', 'periode_selesai' => '2026-10-20',
                'total_biaya' => $id === 1 ? 100000 : 20000, 'adjustment' => $id === 1 ? 5000 : 0,
                'total_setelah_adjustment' => $id === 1 ? 105000 : 20000,
            ]);
        }
        DB::table('pranota_lembur_karyawans')->insert([
            ['pranota_lembur_karyawan_header_id' => 1, 'karyawan_id' => 1, 'tanggal_lembur' => '["2026-10-19"]', 'nominal_awal' => 100000, 'adjustment' => 5000, 'total_akhir' => 105000],
            ['pranota_lembur_karyawan_header_id' => 2, 'karyawan_id' => 2, 'tanggal_lembur' => '["2026-10-19"]', 'nominal_awal' => 10000, 'adjustment' => 0, 'total_akhir' => 10000],
            ['pranota_lembur_karyawan_header_id' => 2, 'karyawan_id' => 3, 'tanggal_lembur' => '["2026-10-19","2026-10-20"]', 'nominal_awal' => 10000, 'adjustment' => 0, 'total_akhir' => 10000],
        ]);
        $user = new User(['username' => 'pranota-test', 'status' => 'approved', 'karyawan_id' => 1]);
        $user->id = 1;
        $user->setRelation('karyawan', null);
        $this->actingAs($user);
        Gate::define('payroll-view', fn () => true);
    }

    public function test_candidates_only_include_new_employees_and_unbilled_dates(): void
    {
        $this->getJson(route('pranota-lembur-karyawan.employee-candidates', 1))
            ->assertOk()->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.karyawan_id', 2)
            ->assertJsonPath('data.0.tanggal_lembur', ['2026-10-20'])
            ->assertJsonPath('data.0.nominal_awal', 15000);
    }

    public function test_append_recalculates_totals_and_puml_without_changing_existing_adjustments(): void
    {
        DB::table('pranota_pumls')->insert(['id' => 1, 'status' => 'draft', 'total_lembur' => 125000, 'total_uang_makan' => 100000, 'grand_total' => 225000]);
        DB::table('pranota_lembur_karyawan_headers')->whereIn('id', [1, 3])->update(['pranota_puml_id' => 1]);
        $url = route('pranota-lembur-karyawan.add-employees', 1);
        $this->postJson($url, ['karyawan_ids' => [2], 'nominal_awal' => 99999999])->assertOk();
        $this->assertDatabaseHas('pranota_lembur_karyawans', ['pranota_lembur_karyawan_header_id' => 1, 'karyawan_id' => 2, 'nominal_awal' => 15000, 'total_akhir' => 15000]);
        $this->assertDatabaseHas('pranota_lembur_karyawans', ['pranota_lembur_karyawan_header_id' => 1, 'karyawan_id' => 1, 'adjustment' => 5000, 'total_akhir' => 105000]);
        $this->assertDatabaseHas('pranota_lembur_karyawan_headers', ['id' => 1, 'total_biaya' => 115000, 'adjustment' => 5000, 'total_setelah_adjustment' => 120000]);
        $this->assertDatabaseHas('pranota_pumls', ['id' => 1, 'total_lembur' => 140000, 'grand_total' => 240000]);
        $this->postJson(route('pranota-lembur-karyawan.refresh-attendance', 1))
            ->assertOk()->assertJsonFragment(['karyawan_id' => 2, 'nominal_awal' => 15000]);
        $this->postJson($url, ['karyawan_ids' => [2]])->assertUnprocessable();
        $this->assertSame(2, DB::table('pranota_lembur_karyawans')->where('pranota_lembur_karyawan_header_id', 1)->count());
    }

    public function test_invalid_selection_is_atomic_and_locked_puml_rejects_changes(): void
    {
        $url = route('pranota-lembur-karyawan.add-employees', 1);
        $this->postJson($url, ['karyawan_ids' => [2, 3]])->assertUnprocessable();
        $this->assertSame(1, DB::table('pranota_lembur_karyawans')->where('pranota_lembur_karyawan_header_id', 1)->count());
        foreach (['approved', 'paid'] as $status) {
            DB::table('pranota_pumls')->updateOrInsert(['id' => 1], ['status' => $status, 'total_lembur' => 105000, 'total_uang_makan' => 0, 'grand_total' => 105000]);
            DB::table('pranota_lembur_karyawan_headers')->where('id', 1)->update(['pranota_puml_id' => 1]);
            $this->postJson($url, ['karyawan_ids' => [2]])->assertForbidden();
            $this->getJson(route('pranota-lembur-karyawan.employee-candidates', 1))->assertForbidden();
        }
    }

    public function test_legacy_period_reservations_and_deleted_pranotas_are_respected(): void
    {
        DB::table('pranota_lembur_karyawans')->where('karyawan_id', 2)->update(['tanggal_lembur' => null]);
        $service = app(PranotaLemburEmployeeService::class);
        $header = PranotaLemburKaryawanHeader::findOrFail(1);
        $this->assertCount(0, $service->candidates($header));
        DB::table('pranota_lembur_karyawan_headers')->where('id', 2)->update(['deleted_at' => now()]);
        $this->assertCount(2, $service->candidates($header));
    }

    public function test_invalid_ids_duplicates_and_missing_permission_do_not_change_pranota(): void
    {
        $url = route('pranota-lembur-karyawan.add-employees', 1);
        foreach ([[], [2, 2], [999], [1, 2]] as $ids) {
            $this->postJson($url, ['karyawan_ids' => $ids])->assertUnprocessable();
        }
        Gate::define('payroll-view', fn () => false);
        $this->postJson($url, ['karyawan_ids' => [2]])->assertForbidden();
        $this->getJson(route('pranota-lembur-karyawan.employee-candidates', 1))->assertForbidden();
        $this->assertSame(1, DB::table('pranota_lembur_karyawans')->where('pranota_lembur_karyawan_header_id', 1)->count());
    }
}
