<?php

namespace Tests\Feature;

use App\Models\LangsirBatam;
use App\Models\MasterPricelistLoloBatam;
use App\Models\SuratJalanBongkaranBatam;
use App\Models\TagihanLoloBatam;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TagihanLoloBatamCompleteTest extends TestCase
{
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'cache.default' => 'array',
            'session.driver' => 'array',
        ]);
        DB::purge('sqlite');

        // Setup audit_logs
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->string('auditable_type')->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->string('action')->nullable();
            $table->string('module')->nullable();
            $table->text('description')->nullable();
            $table->text('old_values')->nullable();
            $table->text('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('url')->nullable();
            $table->timestamps();
        });

        // Setup notifications
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        // Setup roles & role_user
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id');
            $table->foreignId('user_id');
            $table->timestamps();
        });

        // Setup users
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('username')->unique();
            $table->string('email')->nullable();
            $table->string('password')->default('secret');
            $table->unsignedBigInteger('karyawan_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        // Setup master_pricelist_lolo_batams
        Schema::create('master_pricelist_lolo_batams', function (Blueprint $table) {
            $table->id();
            $table->string('size');
            $table->decimal('tarif', 15, 2)->default(0);
            $table->string('status')->default('aktif');
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Setup surat_jalan_bongkaran_batams
        Schema::create('surat_jalan_bongkaran_batams', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat_jalan')->nullable();
            $table->string('nomor_kontainer')->nullable();
            $table->string('size')->nullable();
            $table->string('tipe_kontainer')->nullable();
            $table->string('nama_kapal')->nullable();
            $table->string('voyage')->nullable();
            $table->date('tanggal_bongkar')->nullable();
            $table->boolean('menggunakan_lolo')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        // Setup langsir_batams
        Schema::create('langsir_batams', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_langsir')->nullable();
            $table->string('nomor_kontainer')->nullable();
            $table->string('size')->nullable();
            $table->string('tipe_kontainer')->nullable();
            $table->string('nama_kapal')->nullable();
            $table->string('voyage')->nullable();
            $table->date('tanggal')->nullable();
            $table->boolean('menggunakan_lolo')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        // Setup tagihan_lolo_batams
        Schema::create('tagihan_lolo_batams', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_tagihan')->unique();
            $table->date('tanggal_tagihan');
            $table->string('vendor')->nullable();
            $table->string('kapal')->nullable();
            $table->string('voyage')->nullable();
            $table->enum('status_pembayaran', ['Belum Lunas', 'Lunas'])->default('Belum Lunas');
            $table->date('tanggal_bayar')->nullable();
            $table->decimal('total_tagihan', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->string('status_approval')->default('Pending');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Setup tagihan_lolo_batam_items
        Schema::create('tagihan_lolo_batam_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tagihan_lolo_batam_id');
            $table->unsignedBigInteger('surat_jalan_bongkaran_id')->nullable();
            $table->unsignedBigInteger('langsir_batam_id')->nullable();
            $table->unsignedBigInteger('master_pricelist_lolo_batam_id')->nullable();
            $table->string('sumber_data')->default('manual');
            $table->string('nomor_surat_jalan')->nullable();
            $table->string('nomor_kontainer');
            $table->string('size')->nullable();
            $table->string('tipe_kontainer')->nullable();
            $table->string('kegiatan')->default('LOLO Batam');
            $table->decimal('tarif', 15, 2)->default(0);
            $table->integer('jumlah')->default(1);
            $table->decimal('total', 15, 2)->default(0);
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });

        $this->user = User::create([
            'name' => 'Admin Test',
            'username' => 'admintest',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->user->setRelation('roles', collect());
        $this->user->setRelation('karyawan', null);
        $this->user->setRelation('unreadNotifications', collect());
        $this->user->setRelation('notifications', collect());
        $this->user->setRelation('permissions', collect());

        $this->withoutMiddleware([
            \App\Http\Middleware\EnsureKaryawanPresent::class,
            \App\Http\Middleware\EnsureUserApproved::class,
            \App\Http\Middleware\EnsureCrewChecklistComplete::class,
        ]);

        $this->actingAs($this->user);

        // Grant all permissions for testing
        Gate::before(function () {
            return true;
        });
    }

    public function test_master_pricelist_lolo_batam_crud()
    {
        // 1. Index
        $response = $this->get(route('master.pricelist-lolo-batam.index'));
        $response->assertStatus(200);

        // 2. Create / Store
        $postData = [
            'size' => '20',
            'tarif' => 350000,
            'status' => 'aktif',
            'keterangan' => 'Tarif resmi 2026',
        ];

        $storeResponse = $this->post(route('master.pricelist-lolo-batam.store'), $postData);
        $storeResponse->assertRedirect(route('master.pricelist-lolo-batam.index'));

        $this->assertDatabaseHas('master_pricelist_lolo_batams', [
            'size' => '20',
            'tarif' => 350000,
        ]);

        $pricelist = MasterPricelistLoloBatam::where('size', '20')->first();

        // 3. Edit view
        $editResponse = $this->get(route('master.pricelist-lolo-batam.edit', $pricelist->id));
        $editResponse->assertStatus(200);

        // 4. Update
        $updateResponse = $this->put(route('master.pricelist-lolo-batam.update', $pricelist->id), [
            'size' => '20',
            'tarif' => 375000,
            'status' => 'aktif',
        ]);
        $updateResponse->assertRedirect(route('master.pricelist-lolo-batam.index'));

        $this->assertDatabaseHas('master_pricelist_lolo_batams', [
            'id' => $pricelist->id,
            'tarif' => 375000,
            'size' => '20',
        ]);

        // 5. Delete (Soft delete)
        $deleteResponse = $this->delete(route('master.pricelist-lolo-batam.destroy', $pricelist->id));
        $deleteResponse->assertRedirect(route('master.pricelist-lolo-batam.index'));
        $this->assertSoftDeleted('master_pricelist_lolo_batams', ['id' => $pricelist->id]);
    }

    public function test_tagihan_lolo_batam_pending_api_and_crud()
    {
        // 1. Create pricelist
        $pricelist = MasterPricelistLoloBatam::create([
            'size' => '20',
            'tarif' => 300000,
            'status' => 'aktif',
        ]);

        // 2. Create Surat Jalan Bongkaran with menggunakan_lolo = 1
        $bongkaran = SuratJalanBongkaranBatam::create([
            'nomor_surat_jalan' => 'SJB-001',
            'nomor_kontainer' => 'TBKU1234567',
            'size' => '20',
            'tipe_kontainer' => 'FULL',
            'nama_kapal' => 'KM ALEXINDO 01',
            'voyage' => '01/2026',
            'tanggal_bongkar' => '2026-10-01',
            'menggunakan_lolo' => true,
        ]);

        // 3. Create Langsir Batam with menggunakan_lolo = 1
        $langsir = LangsirBatam::create([
            'nomor_langsir' => 'LGS-001',
            'nomor_kontainer' => 'MRKU9876543',
            'size' => '40',
            'tipe_kontainer' => 'EMPTY',
            'nama_kapal' => 'KM ALEXINDO 02',
            'voyage' => '02/2026',
            'tanggal' => '2026-10-01',
            'menggunakan_lolo' => true,
        ]);

        // Test API pending LOLO
        $apiResponse = $this->getJson(route('tagihan-lolo-batam.api.pending-lolo'));
        $apiResponse->assertStatus(200);
        $apiResponse->assertJsonStructure(['success', 'count', 'data']);
        $this->assertEquals(2, $apiResponse->json('count'));

        // 4. Create invoice
        $postData = [
            'nomor_tagihan' => 'TLB-20261001-0001',
            'tanggal_tagihan' => '2026-10-01',
            'vendor' => 'Pelindo Batam',
            'kapal' => 'KM ALEXINDO 01',
            'voyage' => '01/2026',
            'status_pembayaran' => 'Belum Lunas',
            'keterangan' => 'Tagihan Periode Oktober 2026',
            'items' => [
                [
                    'nomor_kontainer' => 'TBKU1234567',
                    'size' => '20',
                    'tipe_kontainer' => 'FULL',
                    'kegiatan' => 'LOLO Batam',
                    'sumber_data' => 'bongkaran',
                    'surat_jalan_bongkaran_id' => $bongkaran->id,
                    'master_pricelist_lolo_batam_id' => $pricelist->id,
                    'nomor_surat_jalan' => 'SJB-001',
                    'tarif' => 300000,
                    'jumlah' => 1,
                    'keterangan' => 'LOLO Bongkaran Batam',
                ],
                [
                    'nomor_kontainer' => 'MRKU9876543',
                    'size' => '40',
                    'tipe_kontainer' => 'EMPTY',
                    'kegiatan' => 'LOLO Batam',
                    'sumber_data' => 'langsir',
                    'langsir_batam_id' => $langsir->id,
                    'master_pricelist_lolo_batam_id' => null,
                    'nomor_surat_jalan' => 'LGS-001',
                    'tarif' => 500000,
                    'jumlah' => 1,
                    'keterangan' => 'LOLO Langsir Batam',
                ],
            ],
        ];

        $storeResponse = $this->post(route('tagihan-lolo-batam.store'), $postData);
        $this->assertDatabaseHas('tagihan_lolo_batams', [
            'nomor_tagihan' => 'TLB-20261001-0001',
            'total_tagihan' => 800000,
            'status_pembayaran' => 'Belum Lunas',
        ]);

        $this->assertDatabaseHas('tagihan_lolo_batam_items', [
            'nomor_kontainer' => 'TBKU1234567',
            'tarif' => 300000,
            'total' => 300000,
        ]);

        $tagihan = TagihanLoloBatam::where('nomor_tagihan', 'TLB-20261001-0001')->first();
        $storeResponse->assertRedirect(route('tagihan-lolo-batam.show', $tagihan->id));

        // 5. Test Pending LOLO API now excludes the billed containers
        $apiResponseAfter = $this->getJson(route('tagihan-lolo-batam.api.pending-lolo'));
        $this->assertEquals(0, $apiResponseAfter->json('count'));

        // 6. Test Show View
        $showResponse = $this->get(route('tagihan-lolo-batam.show', $tagihan->id));
        $showResponse->assertStatus(200);

        // 7. Test Print View
        $printResponse = $this->get(route('tagihan-lolo-batam.print', $tagihan->id));
        $printResponse->assertStatus(200);

        // 8. Test Edit View
        $editResponse = $this->get(route('tagihan-lolo-batam.edit', $tagihan->id));
        $editResponse->assertStatus(200);

        // 9. Test Update (mark as Lunas with tanggal_bayar)
        $updateResponse = $this->put(route('tagihan-lolo-batam.update', $tagihan->id), [
            'nomor_tagihan' => 'TLB-20261001-0001',
            'tanggal_tagihan' => '2026-10-01',
            'vendor' => 'Pelindo Batam',
            'kapal' => 'KM ALEXINDO 01',
            'voyage' => '01/2026',
            'status_pembayaran' => 'Lunas',
            'tanggal_bayar' => '2026-10-02',
            'keterangan' => 'Sudah ditransfer',
            'items' => [
                [
                    'nomor_kontainer' => 'TBKU1234567',
                    'size' => '20',
                    'tipe_kontainer' => 'FULL',
                    'kegiatan' => 'LOLO Batam',
                    'sumber_data' => 'bongkaran',
                    'surat_jalan_bongkaran_id' => $bongkaran->id,
                    'master_pricelist_lolo_batam_id' => $pricelist->id,
                    'nomor_surat_jalan' => 'SJB-001',
                    'tarif' => 320000, // updated rate
                    'jumlah' => 1,
                    'keterangan' => 'Updated item rate',
                ],
            ],
        ]);

        $updateResponse->assertRedirect(route('tagihan-lolo-batam.show', $tagihan->id));
        $this->assertDatabaseHas('tagihan_lolo_batams', [
            'id' => $tagihan->id,
            'status_pembayaran' => 'Lunas',
            'total_tagihan' => 320000,
        ]);

        // 10. Test Export CSV
        $exportResponse = $this->get(route('tagihan-lolo-batam.export'));
        $exportResponse->assertStatus(200);
        $this->assertStringContainsString('text/csv', $exportResponse->headers->get('Content-Type'));

        // 11. Test Delete (Soft delete)
        $deleteResponse = $this->delete(route('tagihan-lolo-batam.destroy', $tagihan->id));
        $deleteResponse->assertRedirect(route('tagihan-lolo-batam.index'));
        $this->assertSoftDeleted('tagihan_lolo_batams', ['id' => $tagihan->id]);
    }
}
