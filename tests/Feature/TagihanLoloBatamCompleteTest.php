<?php

namespace Tests\Feature;

use App\Models\LangsirBatam;
use App\Models\MasterPricelistLoloBatam;
use App\Models\SuratJalanBongkaranBatam;
use App\Models\TagihanLoloBatam;
use App\Models\TagihanLoloBatamItem;
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
            $table->string('no_kontainer')->nullable();
            $table->string('size')->nullable();
            $table->string('tipe_kontainer')->nullable();
            $table->string('nama_kapal')->nullable();
            $table->string('no_voyage')->nullable();
            $table->string('supir')->nullable();
            $table->string('no_plat')->nullable();
            $table->string('lokasi')->nullable();
            $table->date('tanggal_surat_jalan')->nullable();
            $table->boolean('menggunakan_lolo')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        // Setup langsir_batams
        Schema::create('langsir_batams', function (Blueprint $table) {
            $table->id();
            $table->string('no_transaksi')->nullable();
            $table->string('no_surat_jalan')->nullable();
            $table->string('no_kontainer')->nullable();
            $table->string('size')->nullable();
            $table->string('tipe_kontainer')->nullable();
            $table->string('supir')->nullable();
            $table->string('no_plat')->nullable();
            $table->string('dari')->nullable();
            $table->string('ke')->nullable();
            $table->date('tanggal')->nullable();
            $table->boolean('menggunakan_lolo')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        // Setup karyawans
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap');
            $table->string('nama_panggilan')->nullable();
            $table->string('divisi')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->date('tanggal_berhenti')->nullable();
            $table->timestamps();
        });

        // Setup tagihan_lolo_batams
        Schema::create('tagihan_lolo_batams', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_tagihan')->unique();
            $table->date('tanggal_tagihan');
            $table->string('vendor')->nullable();
            $table->string('tipe_operator')->default('AYP');
            $table->string('operator')->nullable();
            $table->unsignedBigInteger('operator_karyawan_id')->nullable();
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
            $table->string('tipe_operator')->nullable();
            $table->string('operator')->nullable();
            $table->unsignedBigInteger('operator_karyawan_id')->nullable();
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
        $updateData = [
            'size' => '20',
            'tarif' => 375000,
            'status' => 'aktif',
        ];
        $updateResponse = $this->put(route('master.pricelist-lolo-batam.update', $pricelist->id), $updateData);
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
            'no_kontainer' => 'TBKU1234567',
            'size' => '20',
            'tipe_kontainer' => 'FULL',
            'nama_kapal' => 'KM ALEXINDO 01',
            'no_voyage' => '01/2026',
            'tanggal_surat_jalan' => '2026-10-01',
            'menggunakan_lolo' => true,
        ]);

        // 3. Create Langsir Batam with menggunakan_lolo = 1
        $langsir = LangsirBatam::create([
            'no_surat_jalan' => 'LGS-001',
            'no_kontainer' => 'MRKU9876543',
            'size' => '40',
            'tipe_kontainer' => 'EMPTY',
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

    public function test_tagihan_lolo_batam_index_shows_pending_lolo_containers_tab()
    {
        // 1. Create pricelist
        MasterPricelistLoloBatam::create([
            'size' => '20',
            'tarif' => 350000,
            'status' => 'aktif',
        ]);

        // 2. Create Surat Jalan Bongkaran with LOLO = 1 (size 20)
        $bongkaran = SuratJalanBongkaranBatam::create([
            'nomor_surat_jalan' => 'SJB-BATAM-999',
            'no_kontainer' => 'AYPU8889991',
            'size' => '20',
            'tipe_kontainer' => 'FULL',
            'nama_kapal' => 'KM ALEXINDO 08',
            'no_voyage' => 'V08-2026',
            'tanggal_surat_jalan' => '2026-10-01',
            'menggunakan_lolo' => true,
        ]);

        // 3. Create Langsir Batam with LOLO = 1 (size 20FT)
        $langsir = LangsirBatam::create([
            'no_transaksi' => 'TRX-LANGSIR-001',
            'no_surat_jalan' => 'LGS-BATAM-999',
            'no_kontainer' => 'ALLU2202097',
            'size' => '20FT',
            'tipe_kontainer' => 'FULL',
            'tanggal' => '2026-10-01',
            'menggunakan_lolo' => true,
        ]);

        // 4. Access Tagihan LOLO Batam index on kontainer tab
        $response = $this->get(route('tagihan-lolo-batam.index', ['tab' => 'kontainer']));
        $response->assertStatus(200);
        $response->assertSee('SJB-BATAM-999');
        $response->assertSee('AYPU8889991');
        $response->assertSee('ALLU2202097');
        $response->assertSee('Total Tarif');
        $response->assertSee('Rp 350.000');
        $response->assertSee('Belum Masuk Pranota');
        $response->assertDontSee('Buat Tagihan Baru');
    }

    public function test_tagihan_lolo_batam_create_preloads_container_data()
    {
        // 1. Create pricelist
        $pricelist = MasterPricelistLoloBatam::create([
            'size' => '20',
            'tarif' => 350000,
            'status' => 'aktif',
        ]);

        // 2. Create Surat Jalan Bongkaran with LOLO = 1
        $bongkaran = SuratJalanBongkaranBatam::create([
            'nomor_surat_jalan' => 'SJB-PRELOAD-001',
            'no_kontainer' => 'AYPU7771112',
            'size' => '20',
            'tipe_kontainer' => 'FULL',
            'nama_kapal' => 'KM ALEXINDO 09',
            'no_voyage' => 'V09-2026',
            'tanggal_surat_jalan' => '2026-10-01',
            'menggunakan_lolo' => true,
        ]);

        // 3. Access create route with bongkaran_ids parameter
        $response = $this->get(route('tagihan-lolo-batam.create', ['bongkaran_ids' => [$bongkaran->id]]));
        $response->assertStatus(200);
        $response->assertSee('AYPU7771112');
        $response->assertSee('KM ALEXINDO 09');
        $response->assertSee('V09-2026');
    }

    public function test_tagihan_lolo_batam_operator_ayp_and_vendor_support()
    {
        // 1. Create a Karyawan as operator
        $karyawan = \App\Models\Karyawan::create([
            'nama_lengkap' => 'Budi Santoso',
            'nama_panggilan' => 'Budi',
            'divisi' => 'OPERASIONAL',
            'pekerjaan' => 'OPERATOR FORKLIFT',
        ]);

        // 2. Store Tagihan LOLO with AYP Operator
        $postDataAyp = [
            'nomor_tagihan' => 'TLB-OP-AYP-001',
            'tanggal_tagihan' => '2026-10-01',
            'vendor' => 'Pelindo Batam',
            'tipe_operator' => 'AYP',
            'operator_karyawan_id' => $karyawan->id,
            'status_pembayaran' => 'Belum Lunas',
            'items' => [
                [
                    'nomor_kontainer' => 'AYPU1112223',
                    'size' => '20',
                    'tipe_kontainer' => 'FULL',
                    'kegiatan' => 'LOLO Batam',
                    'tarif' => 400000,
                    'jumlah' => 1,
                ],
            ],
        ];

        $responseAyp = $this->post(route('tagihan-lolo-batam.store'), $postDataAyp);
        $this->assertDatabaseHas('tagihan_lolo_batams', [
            'nomor_tagihan' => 'TLB-OP-AYP-001',
            'tipe_operator' => 'AYP',
            'operator' => 'Budi Santoso',
            'operator_karyawan_id' => $karyawan->id,
        ]);

        // 3. Store Tagihan LOLO with Vendor Operator
        $postDataVendor = [
            'nomor_tagihan' => 'TLB-OP-VND-001',
            'tanggal_tagihan' => '2026-10-01',
            'vendor' => 'PT Trans Logistik',
            'tipe_operator' => 'VENDOR',
            'operator' => 'Vendor Trans Indo',
            'status_pembayaran' => 'Belum Lunas',
            'items' => [
                [
                    'nomor_kontainer' => 'AYPU3334445',
                    'size' => '40',
                    'tipe_kontainer' => 'FULL',
                    'kegiatan' => 'LOLO Batam',
                    'tarif' => 600000,
                    'jumlah' => 1,
                ],
            ],
        ];

        $responseVendor = $this->post(route('tagihan-lolo-batam.store'), $postDataVendor);
        $this->assertDatabaseHas('tagihan_lolo_batams', [
            'nomor_tagihan' => 'TLB-OP-VND-001',
            'tipe_operator' => 'VENDOR',
            'operator' => 'Vendor Trans Indo',
        ]);

        // 4. View show page and verify operator badges
        $tagihanAyp = TagihanLoloBatam::where('nomor_tagihan', 'TLB-OP-AYP-001')->first();
        $responseShow = $this->get(route('tagihan-lolo-batam.show', $tagihanAyp->id));
        $responseShow->assertStatus(200);
        $responseShow->assertSee('AYP: Budi Santoso');
    }

    public function test_pranota_lolo_batam_dedicated_menu_and_views()
    {
        // 1. Create a TagihanLoloBatam entry
        $pranota = TagihanLoloBatam::create([
            'nomor_tagihan' => 'PLB/10/26/000001',
            'tanggal_tagihan' => '2026-10-01',
            'vendor' => 'Pelindo Batam',
            'tipe_operator' => 'AYP',
            'operator' => 'Operator Test',
            'kapal' => 'KM ALEXINDO 01',
            'voyage' => 'VOY 09',
            'status_pembayaran' => 'Belum Lunas',
            'total_tagihan' => 700000,
            'created_by' => $this->user->id,
        ]);

        TagihanLoloBatamItem::create([
            'tagihan_lolo_batam_id' => $pranota->id,
            'nomor_kontainer' => 'AYPU9988776',
            'size' => '20',
            'tipe_kontainer' => 'FULL',
            'kegiatan' => 'LOLO Batam',
            'tarif' => 350000,
            'jumlah' => 2,
            'total' => 700000,
        ]);

        // 2. Index Pranota LOLO Batam
        $response = $this->get(route('pranota-lolo-batam.index'));
        $response->assertStatus(200);
        $response->assertSee('Daftar Pranota LOLO Batam');
        $response->assertSee('PLB/10/26/000001');

        // 3. Show Pranota
        $showResponse = $this->get(route('pranota-lolo-batam.show', $pranota->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('PLB/10/26/000001');
        $showResponse->assertSee('AYPU9988776');

        // 4. Print Pranota
        $printResponse = $this->get(route('pranota-lolo-batam.print', $pranota->id));
        $printResponse->assertStatus(200);
        $printResponse->assertSee('PRANOTA BIAYA LOLO');
        $printResponse->assertSee('PLB/10/26/000001');

        // 5. Edit Pranota
        $editResponse = $this->get(route('pranota-lolo-batam.edit', $pranota->id));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('PLB/10/26/000001');

        // 6. Export Pranota CSV
        $exportResponse = $this->get(route('pranota-lolo-batam.export'));
        $exportResponse->assertStatus(200);
    }
}
