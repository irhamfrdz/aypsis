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

    public function test_other_activities_and_bl_are_rejected(): void
    {
        foreach (['muat', 'bongkar', null] as $kegiatan) {
            $payload = array_replace($this->payload(), ['type' => 'naik_kapal', 'kegiatan' => $kegiatan]);
            $this->postJson(route('ob.mark-mobil-panjang'), $payload)->assertUnprocessable()->assertJsonValidationErrors('kegiatan');
            $this->getJson(route('ob.mobil-panjang-candidates', ['type' => 'naik_kapal', 'id' => 1, 'kegiatan' => $kegiatan]))->assertUnprocessable();
        }
        $this->postJson(route('ob.mark-mobil-panjang'), $this->payload() + ['kegiatan' => 'muat_temas'])->assertUnprocessable()->assertJsonValidationErrors('type');
        $this->assertDatabaseHas('bls', ['id' => 1, 'sudah_ob' => false]);
        auth()->user()->setRelation('permissions', collect());
        Gate::define('ob-view', fn () => false);
        $this->postJson(route('ob.mark-mobil-panjang'), $this->payload())->assertForbidden();
    }

    public function test_pair_validation_and_atomic_rollback(): void
    {
        foreach ([1, 2] as $id) {
            DB::table('naik_kapal')->insert(['id' => $id, 'nomor_kontainer' => 'TEST'.$id, 'nama_kapal' => 'KAPAL UJI', 'no_voyage' => '001', 'size_kontainer' => '20']);
        }
        foreach ([['size_kontainer' => '40'], ['no_voyage' => '002'], ['nama_kapal' => 'OTHER'], ['nomor_kontainer' => 'TEST1'], ['sudah_ob' => true]] as $change) {
            DB::table('naik_kapal')->where('id', 2)->update(array_replace(['size_kontainer' => '20', 'no_voyage' => '001', 'nama_kapal' => 'KAPAL UJI', 'nomor_kontainer' => 'TEST2', 'sudah_ob' => false], $change));
            try {
                app(ObMobilPanjangService::class)->process('naik_kapal', [1, 2], fn () => $this->fail('Invalid pair must not be processed.'));
                $this->fail('Invalid pair must be rejected.');
            } catch (ValidationException $e) {
                $this->assertArrayHasKey('pasangan_id', $e->errors());
            }
        }
        DB::table('naik_kapal')->where('id', 2)->update(['sudah_ob' => false]);
        try {
            app(ObMobilPanjangService::class)->process('naik_kapal', [1, 2], function ($record) {
                if ($record->id === 2) throw ValidationException::withMessages(['pair' => 'Gagal']);
                DB::table('naik_kapal')->where('id', $record->id)->update(['sudah_ob' => true]);
            });
            $this->fail('Failure must abort the pair.');
        } catch (ValidationException $e) {
            $this->assertDatabaseHas('naik_kapal', ['id' => 1, 'sudah_ob' => false, 'ob_mobil_panjang' => false]);
        }
    }

    public function test_muat_temas_pair_creates_125000_bills_and_matching_movement_history(): void
    {
        Schema::table('naik_kapal', function (Blueprint $table) {
            $table->string('asal_kontainer')->nullable();
            $table->string('jenis_barang')->nullable();
        });
        Schema::table('karyawans', function (Blueprint $table) {
            $table->string('nama_panggilan')->nullable();
            $table->string('nama_lengkap')->nullable();
        });
        DB::table('karyawans')->where('id', 1)->update(['nama_panggilan' => 'SUPIR UJI']);
        Schema::create('tagihan_ob', function (Blueprint $table) {
            $table->id();
            foreach (['naik_kapal_id', 'created_by'] as $column) $table->unsignedBigInteger($column);
            foreach (['kapal', 'voyage', 'kegiatan', 'nomor_kontainer', 'size_kontainer', 'nama_supir', 'barang', 'status_kontainer', 'keterangan'] as $column) $table->text($column);
            $table->dateTime('tanggal_ob');
            $table->boolean('is_combo');
            $table->decimal('biaya');
            $table->timestamps();
        });
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('auditable_id');
            foreach (['user_name', 'auditable_type', 'action', 'module', 'description', 'old_values', 'new_values', 'ip_address', 'user_agent', 'url'] as $column) $table->text($column)->nullable();
            $table->timestamp('created_at')->nullable();
        });
        foreach ([1, 2] as $id) {
            DB::table('naik_kapal')->insert([
                'id' => $id, 'nomor_kontainer' => 'TEMAS'.$id, 'nama_kapal' => 'KAPAL UJI',
                'no_voyage' => '001', 'size_kontainer' => '20', 'tipe_kontainer' => 'FCL',
                'asal_kontainer' => 'DEPO', 'jenis_barang' => 'BARANG UJI',
            ]);
        }
        Schema::table('karyawans', function (Blueprint $table) {
            $table->string('divisi')->default('SUPIR');
            $table->date('tanggal_berhenti')->nullable();
        });
        Schema::table('bls', fn (Blueprint $table) => $table->string('asal_kontainer')->nullable());
        Schema::table('history_kontainers', fn (Blueprint $table) => $table->unsignedBigInteger('asal_gudang_id')->nullable());
        Schema::table('tagihan_ob', function (Blueprint $table) {
            $table->string('nomor_surat_jalan')->nullable();
            $table->unsignedBigInteger('gudang_asal_id')->nullable();
            $table->boolean('is_ckls_mobil_panjang')->default(false);
        });
        DB::table('gudangs')->where('id', 1)->update(['nama_gudang' => 'TEMAS JKT']);
        Schema::create('master_pricelist_ob_antar_gudang', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gudang_tujuan_id');
            $table->string('size_kontainer');
            $table->string('status_kontainer');
            $table->decimal('biaya');
        });
        DB::table('master_pricelist_ob_antar_gudang')->insert(['id' => 1, 'gudang_tujuan_id' => 1, 'size_kontainer' => '20ft', 'status_kontainer' => 'full', 'biaya' => 300000]);
        Schema::create('manifests', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_kontainer');
            $table->string('nama_kapal');
            $table->string('no_voyage');
        });
        foreach ([1, 2] as $id) {
            DB::table('bls')->where('id', $id)->update(['nomor_kontainer' => 'TEMAS'.$id]);
            DB::table('manifests')->insert(['nomor_kontainer' => 'TEMAS'.$id, 'nama_kapal' => 'KAPAL UJI', 'no_voyage' => '001']);
        }
        $payload = [
            'naik_kapal_id' => 1, 'pasangan_id' => 2, 'kegiatan' => 'muat_temas',
            'tanggal_ob' => '2026-11-24', 'nomor_surat_jalan' => 'SC 123',
            'status_kontainer' => 'full', 'pricelist_id' => 1, 'gudang_asal_id' => 1,
            'supir_id' => 1, 'is_ckls_mobil_panjang' => true,
        ];
        $this->getJson(route('ob.mobil-panjang-candidates', ['type' => 'naik_kapal', 'id' => 1, 'kegiatan' => 'muat_temas']))
            ->assertOk()->assertJsonPath('eligible', true)->assertJsonCount(1, 'candidates');
        $this->postJson(route('ob.process-tl'), array_replace($payload, ['pasangan_id' => null]))->assertUnprocessable();
        $this->postJson(route('ob.process-tl'), $payload)->assertOk()->assertJsonPath('success', true);
        $this->assertDatabaseCount('tagihan_ob', 2);
        $this->assertEquals(250000, DB::table('tagihan_ob')->sum('biaya'));
        foreach ([1, 2] as $id) {
            $this->assertDatabaseHas('tagihan_ob', ['naik_kapal_id' => $id, 'kegiatan' => 'MUAT TEMAS', 'biaya' => 125000, 'nomor_surat_jalan' => 'SC 123', 'tanggal_ob' => '2026-11-24']);
            $this->assertDatabaseHas('naik_kapal', ['id' => $id, 'sudah_ob' => true, 'ob_mobil_panjang' => true]);
            $this->assertDatabaseHas('history_kontainers', [
                'nomor_kontainer' => 'TEMAS'.$id, 'jenis_kegiatan' => 'Pindahan Gudang',
                'keterangan' => 'OB Muat Temas: TEMAS JKT -> TEMAS JKT. Surat Jalan: SC 123. Supir: SUPIR UJI. Kapal: KAPAL UJI. Voyage: 001',
            ]);
        }
        Schema::create('pranota_ob_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_type');
            $table->unsignedBigInteger('item_id');
        });
        Schema::create('pranota_ob_antar_gudang_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tagihan_ob_id');
        });
        (require database_path('migrations/2026_10_09_000004_create_pranota_ob_muat_temas_tables.php'))->up();
        $this->postJson(route('pranota-ob-muat-temas.store'), [
            'nama_kapal' => 'KAPAL UJI', 'no_voyage' => '001',
            'nomor_pranota' => 'PMT-11-26-000001', 'tanggal_ob' => '2026-11-25',
            'items' => [['id' => 1, 'type' => 'naik_kapal'], ['id' => 2, 'type' => 'naik_kapal']],
        ])->assertOk()->assertJsonPath('success', true);
        $this->assertDatabaseHas('pranota_ob_muat_temas', ['nominal' => 250000, 'grand_total' => 250000]);
        DB::table('naik_kapal')->insert(['id' => 3, 'nomor_kontainer' => 'TEST3', 'nama_kapal' => 'KAPAL UJI', 'no_voyage' => '001', 'size_kontainer' => '20', 'tipe_kontainer' => 'FCL']);
        DB::table('manifests')->insert(['nomor_kontainer' => 'TEST3', 'nama_kapal' => 'KAPAL UJI', 'no_voyage' => '001']);
        $this->postJson(route('ob.process-tl'), array_replace($payload, ['naik_kapal_id' => 3, 'pasangan_id' => null, 'is_ckls_mobil_panjang' => false]))->assertOk();
        $this->assertDatabaseHas('tagihan_ob', ['naik_kapal_id' => 3, 'biaya' => 300000, 'is_combo' => false]);
        $this->assertDatabaseHas('naik_kapal', ['id' => 3, 'sudah_ob' => true, 'ob_mobil_panjang' => false]);
    }
}
