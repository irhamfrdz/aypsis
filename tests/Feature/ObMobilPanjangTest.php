<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureCrewChecklistComplete;
use App\Http\Middleware\EnsureKaryawanPresent;
use App\Http\Middleware\EnsureUserApproved;
use App\Models\Permission;
use App\Models\User;
use App\Services\ObMobilPanjangService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ObMobilPanjangTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username');
        });
        foreach (['bls', 'naik_kapal'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                foreach (['nomor_kontainer', 'nama_kapal', 'no_voyage', 'size_kontainer', 'tipe_kontainer', 'ke', 'catatan_ob'] as $column) {
                    $table->string($column)->nullable();
                }
                $table->boolean('sudah_ob')->default(false);
                $table->boolean('sudah_tl')->default(false);
                $table->boolean('is_tl')->default(false);
                $table->unsignedBigInteger('supir_id')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->dateTime('tanggal_ob')->nullable();
                $table->float('tonnage')->default(0);
                $table->float('volume')->default(0);
                $table->float('max_tv')->default(0);
                $table->timestamps();
            });
        }
        (require database_path('migrations/2026_11_25_120000_add_mobil_panjang_to_ob_records.php'))->up();
        Schema::create('gudangs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_gudang');
        });
        Schema::create('karyawans', fn (Blueprint $table) => $table->id());
        foreach (['kontainers', 'stock_kontainers'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                $table->string('nomor_seri_gabungan');
                $table->unsignedBigInteger('gudangs_id')->nullable();
                $table->timestamps();
            });
        }
        Schema::create('history_kontainers', function (Blueprint $table) {
            $table->id();
            foreach (['nomor_kontainer', 'tipe_kontainer', 'jenis_kegiatan', 'keterangan'] as $column) {
                $table->text($column);
            }
            $table->date('tanggal_kegiatan');
            $table->unsignedBigInteger('gudang_id');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });
        DB::table('users')->insert(['id' => 1, 'username' => 'ob-test']);
        $user = User::find(1);
        $user->setRelation('permissions', collect([new Permission(['name' => 'ob-view'])]));
        $this->actingAs($user);
        Gate::define('ob-view', fn () => true);
        $this->withoutMiddleware([EnsureKaryawanPresent::class, EnsureUserApproved::class, EnsureCrewChecklistComplete::class]);
        DB::table('gudangs')->insert(['id' => 1, 'nama_gudang' => 'MERATUS JKT']);
        DB::table('karyawans')->insert(['id' => 1]);
        foreach ([1, 2, 3] as $id) {
            DB::table('bls')->insert([
                'id' => $id, 'nomor_kontainer' => 'TEST'.$id, 'nama_kapal' => 'KAPAL UJI',
                'no_voyage' => '001', 'size_kontainer' => '20', 'tipe_kontainer' => 'FCL',
            ]);
            DB::table('kontainers')->insert(['nomor_seri_gabungan' => 'TEST'.$id]);
        }
    }

    private function payload(): array
    {
        return ['type' => 'bl', 'id' => 1, 'pasangan_id' => 2, 'supir_id' => 1, 'ke_gudang_id' => 1];
    }

    public function test_pair_marks_both_containers_and_records_both_movements(): void
    {
        $this->postJson(route('ob.mark-mobil-panjang'), $this->payload())->assertOk()->assertJsonPath('success', true);
        foreach ([1 => 2, 2 => 1] as $id => $partner) {
            $this->assertDatabaseHas('bls', ['id' => $id, 'sudah_ob' => true, 'ob_mobil_panjang' => true, 'ob_pasangan_id' => $partner, 'supir_id' => 1, 'ke' => 'MERATUS JKT']);
            $this->assertDatabaseHas('kontainers', ['nomor_seri_gabungan' => 'TEST'.$id, 'gudangs_id' => 1]);
            $this->assertDatabaseHas('history_kontainers', ['nomor_kontainer' => 'TEST'.$id, 'gudang_id' => 1]);
        }
        $this->assertDatabaseHas('bls', ['id' => 3, 'sudah_ob' => false]);
        $this->assertDatabaseCount('history_kontainers', 2);
        $this->postJson(route('ob.mark-mobil-panjang'), $this->payload())->assertUnprocessable();
        $this->assertDatabaseCount('history_kontainers', 2);
        $this->assertSame(250000, ObMobilPanjangService::BIAYA_PER_KONTAINER * 2);
    }

    public function test_invalid_pair_is_rejected_before_any_changes(): void
    {
        foreach ([['size_kontainer' => '40'], ['no_voyage' => '002'], ['nama_kapal' => 'KAPAL LAIN'], ['nomor_kontainer' => 'TEST1'], ['sudah_ob' => true]] as $change) {
            DB::table('bls')->where('id', 2)->update(['size_kontainer' => '20', 'no_voyage' => '001', 'nama_kapal' => 'KAPAL UJI', 'nomor_kontainer' => 'TEST2', 'sudah_ob' => false]);
            DB::table('bls')->where('id', 2)->update($change);
            $this->postJson(route('ob.mark-mobil-panjang'), $this->payload())->assertUnprocessable();
            $this->assertDatabaseHas('bls', ['id' => 1, 'sudah_ob' => false]);
            $this->assertDatabaseCount('history_kontainers', 0);
        }
    }

    public function test_pranota_keeps_125000_per_container_even_if_request_has_different_cost(): void
    {
        Schema::create('master_pricelist_ob', function (Blueprint $table) {
            $table->id();
            $table->string('size_kontainer');
            $table->string('status_kontainer');
            $table->decimal('biaya');
        });
        Schema::create('tagihan_ob', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('naik_kapal_id')->nullable();
            $table->string('kegiatan');
        });
        Schema::create('pranota_obs', function (Blueprint $table) {
            $table->id();
            foreach (['nomor_pranota', 'nama_kapal', 'no_voyage', 'tanggal_ob', 'nomor_accurate', 'keterangan', 'items'] as $column) {
                $table->text($column)->nullable();
            }
            $table->decimal('adjustment')->default(0);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });
        Schema::create('pranota_ob_items', function (Blueprint $table) {
            $table->id();
            foreach (['pranota_ob_id', 'item_id', 'created_by'] as $column) {
                $table->unsignedBigInteger($column);
            }
            foreach (['item_type', 'nomor_kontainer', 'nama_barang', 'supir', 'size', 'status'] as $column) {
                $table->string($column)->nullable();
            }
            $table->decimal('biaya');
            $table->timestamps();
        });
        $this->postJson(route('ob.mark-mobil-panjang'), $this->payload())->assertOk();
        $this->postJson(route('ob.masuk-pranota'), [
            'nama_kapal' => 'KAPAL UJI', 'no_voyage' => '001',
            'nomor_pranota' => 'POB-UJI-001', 'tanggal_ob' => '2026-11-25',
            'items' => [
                ['id' => 1, 'type' => 'bl', 'biaya' => 1],
                ['id' => 2, 'type' => 'bl', 'biaya' => 999999],
            ],
        ])->assertOk()->assertJsonPath('success', true);
        $this->assertEquals(250000, DB::table('pranota_ob_items')->sum('biaya'));
        $items = json_decode(DB::table('pranota_obs')->value('items'), true);
        $this->assertSame([125000, 125000], array_column($items, 'biaya'));
    }

    public function test_second_container_failure_rolls_back_first_container(): void
    {
        try {
            app(ObMobilPanjangService::class)->process('bl', [1, 2], function ($record) {
                if ($record->id === 2) {
                    throw ValidationException::withMessages(['pair' => 'Gagal memproses pasangan']);
                }
                DB::table('bls')->where('id', $record->id)->update(['sudah_ob' => true]);
            });
            $this->fail('Failure should abort the entire pair.');
        } catch (ValidationException $e) {
            $this->assertDatabaseHas('bls', ['id' => 1, 'sudah_ob' => false, 'ob_mobil_panjang' => false]);
        }
    }

    public function test_candidates_and_permission_are_checked(): void
    {
        DB::table('bls')->where('id', 3)->update(['no_voyage' => 'OTHER']);
        $this->getJson(route('ob.mobil-panjang-candidates', ['type' => 'bl', 'id' => 1]))
            ->assertOk()->assertJsonPath('eligible', true)->assertJsonCount(1, 'candidates')->assertJsonPath('candidates.0.id', 2);
        $this->postJson(route('ob.mark-mobil-panjang'), array_replace($this->payload(), ['pasangan_id' => 1]))->assertUnprocessable();
        auth()->user()->setRelation('permissions', collect());
        Gate::define('ob-view', fn () => false);
        $this->postJson(route('ob.mark-mobil-panjang'), $this->payload())->assertForbidden();
    }
}
