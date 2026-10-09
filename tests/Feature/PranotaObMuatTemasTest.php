<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureCrewChecklistComplete;
use App\Http\Middleware\EnsureKaryawanPresent;
use App\Http\Middleware\EnsureUserApproved;
use App\Http\Controllers\PranotaObController;
use App\Models\NaikKapal;
use App\Models\PranotaObMuatTemas;
use App\Models\PranotaOb;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PranotaObMuatTemasTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'database.connections.sqlite.foreign_key_constraints' => true]);
        DB::purge('sqlite');
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username');
        });
        Schema::create('naik_kapal', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_kontainer');
            $table->string('nama_kapal');
            $table->string('no_voyage');
            $table->string('ke');
            $table->boolean('sudah_ob');
        });
        Schema::create('tagihan_ob', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('naik_kapal_id');
            $table->string('kegiatan');
            $table->date('tanggal_ob');
            $table->string('nomor_kontainer');
            $table->string('nama_supir');
            $table->string('barang');
            $table->string('size_kontainer');
            $table->string('status_kontainer');
            $table->decimal('biaya', 15, 2);
            $table->string('nomor_surat_jalan');
            $table->unsignedBigInteger('gudang_asal_id')->nullable();
            $table->boolean('is_ckls_mobil_panjang');
            $table->text('keterangan');
            $table->timestamps();
        });
        Schema::create('pranota_ob_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pranota_ob_id')->nullable();
            $table->string('item_type');
            $table->unsignedBigInteger('item_id');
        });
        Schema::create('pranota_ob_antar_gudang_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tagihan_ob_id');
        });
        Schema::create('pranota_obs', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pranota');
            $table->string('nama_kapal');
            $table->string('no_voyage');
            $table->json('items')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
        (require database_path('migrations/2026_10_09_000004_create_pranota_ob_muat_temas_tables.php'))->up();
        DB::table('users')->insert(['id' => 1, 'name' => 'Penguji Temas', 'username' => 'temas-test']);
        $user = new User;
        $user->setRawAttributes(['id' => 1, 'name' => 'Penguji Temas', 'username' => 'temas-test']);
        $this->actingAs($user);
        Gate::define('ob-view', fn () => true);
        Gate::define('pranota-ob-view', fn () => true);
        $this->withoutMiddleware([EnsureKaryawanPresent::class, EnsureUserApproved::class, EnsureCrewChecklistComplete::class]);
    }

    private function container(int $id, int $biaya = 250000, string $kegiatan = 'MUAT TEMAS'): void
    {
        DB::table('naik_kapal')->insert([
            'id' => $id, 'nomor_kontainer' => 'TEST'.$id, 'nama_kapal' => 'KAPAL UJI',
            'no_voyage' => '001', 'ke' => 'TEMAS JKT', 'sudah_ob' => true,
        ]);
        DB::table('tagihan_ob')->insert([
            'id' => $id, 'naik_kapal_id' => $id, 'kegiatan' => $kegiatan,
            'tanggal_ob' => '2026-10-01', 'nomor_kontainer' => 'TEST'.$id,
            'nama_supir' => 'SUPIR UJI', 'barang' => 'BARANG UJI', 'size_kontainer' => '20',
            'status_kontainer' => 'full', 'biaya' => $biaya, 'nomor_surat_jalan' => 'SC 0011687',
            'is_ckls_mobil_panjang' => $biaya === 250000,
            'keterangan' => 'OB Muat Temas - TEMAS JKT - Pricelist #1',
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function payload(array $ids): array
    {
        return [
            'nomor_pranota' => 'PMT-10-26-000001', 'tanggal_ob' => '2026-10-09',
            'nama_kapal' => 'KAPAL UJI', 'no_voyage' => '001', 'adjustment' => -25000,
            'items' => array_map(fn ($id) => ['id' => $id, 'type' => 'naik_kapal', 'biaya' => 1], $ids),
        ];
    }

    public function test_cost_comes_from_tagihan_and_print_keeps_original_snapshot(): void
    {
        $this->container(1);
        $this->container(2, 325000);
        $this->postJson(route('pranota-ob-muat-temas.store'), $this->payload([1, 2]))->assertOk()->assertJsonPath('success', true);
        $pranota = PranotaObMuatTemas::with('items')->firstOrFail();
        $this->assertEquals(575000, $pranota->nominal);
        $this->assertEquals(550000, $pranota->grand_total);
        $this->assertEquals(250000, $pranota->items->first()->snapshot->biaya);
        $this->assertSame('SC 0011687', $pranota->items->first()->snapshot->nomor_surat_jalan);
        DB::table('tagihan_ob')->where('id', 1)->update(['biaya' => 1, 'nama_supir' => 'DIUBAH']);
        $this->get(route('pranota-ob.muat-temas.print', $pranota))->assertOk()
            ->assertSee('PRANOTA OB MUAT TEMAS')->assertSee('Half-Folio')
            ->assertSee('250.000')->assertSee('SUPIR UJI')->assertDontSee('DIUBAH');
    }

    public function test_same_tagihan_cannot_be_billed_twice(): void
    {
        $this->container(1);
        $this->postJson(route('pranota-ob-muat-temas.store'), $this->payload([1]))->assertOk();
        $payload = $this->payload([1]);
        $payload['nomor_pranota'] = 'PMT-10-26-000002';
        $this->postJson(route('pranota-ob-muat-temas.store'), $payload)->assertUnprocessable()->assertJsonValidationErrors('items');
        $this->assertDatabaseCount('pranota_ob_muat_temas', 1);
        $this->assertDatabaseCount('pranota_ob_muat_temas_items', 1);
    }

    public function test_pranota_ob_list_combines_both_types_with_colliding_ids_and_search(): void
    {
        $this->container(1);
        $this->postJson(route('pranota-ob-muat-temas.store'), $this->payload([1]))->assertOk();
        PranotaOb::create([
            'nomor_pranota' => 'POB-10-26-000001', 'nama_kapal' => 'KAPAL UJI',
            'no_voyage' => '001', 'items' => [], 'created_by' => 1,
        ]);
        $data = app(PranotaObController::class)->index(Request::create('/pranota-ob'))->getData();
        $this->assertSame(2, $data['pranotas']->total());
        $this->assertSame(2, $data['stats']['total']);
        $this->assertEqualsCanonicalizing(['ob', 'muat_temas'], $data['pranotas']->pluck('jenis_pranota')->all());
        $this->assertSame([1, 1], $data['pranotas']->pluck('id')->all());
        $filtered = app(PranotaObController::class)->index(Request::create('/pranota-ob', 'GET', ['search' => 'PMT']))->getData();
        $this->assertSame(1, $filtered['pranotas']->total());
        $this->assertSame('muat_temas', $filtered['pranotas']->first()->jenis_pranota);
    }

    public function test_mixed_activity_is_rejected_without_partial_pranota(): void
    {
        $this->container(1);
        $this->container(2, 325000, 'ANTAR GUDANG');
        $this->postJson(route('pranota-ob-muat-temas.store'), $this->payload([1, 2]))->assertUnprocessable()->assertJsonValidationErrors('items');
        $this->assertDatabaseCount('pranota_ob_muat_temas', 0);
        $this->assertDatabaseCount('pranota_ob_muat_temas_items', 0);
    }

    public function test_wrong_voyage_and_existing_regular_pranota_are_rejected(): void
    {
        $this->container(1);
        $payload = $this->payload([1]);
        $payload['no_voyage'] = 'OTHER';
        $this->postJson(route('pranota-ob-muat-temas.store'), $payload)->assertUnprocessable()->assertJsonValidationErrors('items');
        DB::table('pranota_ob_items')->insert(['item_type' => NaikKapal::class, 'item_id' => 1]);
        $this->postJson(route('pranota-ob-muat-temas.store'), $this->payload([1]))->assertUnprocessable()->assertJsonValidationErrors('items');
        $this->assertDatabaseCount('pranota_ob_muat_temas', 0);
    }
}
