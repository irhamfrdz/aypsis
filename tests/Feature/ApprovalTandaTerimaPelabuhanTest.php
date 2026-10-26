<?php

namespace Tests\Feature;

use App\Http\Controllers\ApprovalTandaTerimaPelabuhanController;
use App\Models\TandaTerima;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ApprovalTandaTerimaPelabuhanTest extends TestCase
{
    private $dispatcher;

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        $this->dispatcher = Model::getEventDispatcher();
        Model::unsetEventDispatcher();

        Schema::create('surat_jalans', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_checkpoint')->nullable();
            $table->string('tipe_kontainer')->nullable();
            $table->boolean('lembur')->nullable();
            $table->boolean('nginap')->nullable();
            $table->boolean('tidak_lembur_nginap')->nullable();
            $table->timestamps();
        });
        Schema::create('gudangs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_gudang');
            $table->string('status');
        });
        foreach (['kontainers', 'stock_kontainers'] as $tableName) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('nomor_seri_gabungan');
                $table->string('tipe_kontainer')->nullable();
                $table->string('status')->nullable();
                $table->unsignedBigInteger('gudangs_id')->nullable();
                $table->timestamps();
            });
        }
        Schema::create('history_kontainers', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_kontainer');
            $table->string('tipe_kontainer')->nullable();
            $table->string('jenis_kegiatan');
            $table->date('tanggal_kegiatan');
            $table->unsignedBigInteger('asal_gudang_id')->nullable();
            $table->unsignedBigInteger('gudang_id');
            $table->string('keterangan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
        Schema::create('tanda_terimas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('surat_jalan_id')->nullable();
            $table->string('no_surat_jalan');
            $table->date('tanggal')->nullable();
            $table->date('tanggal_checkpoint_supir')->nullable();
            $table->string('no_kontainer')->nullable();
            $table->boolean('lembur')->nullable();
            $table->boolean('nginap')->nullable();
            $table->boolean('tidak_lembur_nginap')->nullable();
            $table->string('supir_pengganti')->nullable();
            $table->string('approval_pelabuhan_status')->default('pending');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Model::setEventDispatcher($this->dispatcher);
        parent::tearDown();
    }

    public function test_approval_requires_both_dates_and_updates_surat_jalan_checkpoint(): void
    {
        $gudangId = DB::table('gudangs')->insertGetId(['nama_gudang' => 'Gudang A', 'status' => 'aktif']);
        $suratJalanId = DB::table('surat_jalans')->insertGetId([]);
        $id = DB::table('tanda_terimas')->insertGetId([
            'surat_jalan_id' => $suratJalanId,
            'no_surat_jalan' => 'SJ-001',
        ]);
        $tandaTerima = TandaTerima::findOrFail($id);
        $request = Request::create('/approval-tanda-terima-pelabuhan/'.$id, 'PATCH', [
            'tanggal' => '2026-10-26',
            'tanggal_checkpoint_supir' => '2026-10-25',
            'supir_pengganti' => 'Budi',
            'lembur' => '1',
            'nginap' => '1',
            'gudang_id' => $gudangId,
        ]);
        $request->setUserResolver(fn () => (object) ['id' => 7]);

        (new ApprovalTandaTerimaPelabuhanController)->approve($request, $tandaTerima);

        $saved = $tandaTerima->fresh();
        $this->assertSame('2026-10-26', $saved->tanggal->format('Y-m-d'));
        $this->assertSame('2026-10-25', $saved->tanggal_checkpoint_supir->format('Y-m-d'));
        $this->assertSame('Budi', $saved->supir_pengganti);
        $this->assertTrue($saved->lembur);
        $this->assertTrue($saved->nginap);
        $this->assertFalse($saved->tidak_lembur_nginap);
        $this->assertSame('approved', $saved->approval_pelabuhan_status);
        $this->assertSame(7, (int) $saved->updated_by);
        $this->assertDatabaseHas('surat_jalans', [
            'id' => $suratJalanId,
            'tanggal_checkpoint' => '2026-10-25',
            'lembur' => 1,
            'nginap' => 1,
            'tidak_lembur_nginap' => 0,
        ]);
    }

    public function test_approval_rejects_missing_dates(): void
    {
        $id = DB::table('tanda_terimas')->insertGetId(['no_surat_jalan' => 'SJ-002']);
        $request = Request::create('/approval-tanda-terima-pelabuhan/'.$id, 'PATCH', ['tanggal' => '2026-10-26']);

        $this->expectException(ValidationException::class);
        (new ApprovalTandaTerimaPelabuhanController)->approve($request, TandaTerima::findOrFail($id));
    }

    public function test_approval_requires_a_lembur_or_nginap_choice(): void
    {
        $gudangId = DB::table('gudangs')->insertGetId(['nama_gudang' => 'Gudang A', 'status' => 'aktif']);
        $id = DB::table('tanda_terimas')->insertGetId(['no_surat_jalan' => 'SJ-004']);
        $request = Request::create('/approval-tanda-terima-pelabuhan/'.$id, 'PATCH', [
            'tanggal' => '2026-10-26',
            'tanggal_checkpoint_supir' => '2026-10-25',
            'gudang_id' => $gudangId,
        ]);

        $this->expectException(ValidationException::class);
        (new ApprovalTandaTerimaPelabuhanController)->approve($request, TandaTerima::findOrFail($id));
    }

    public function test_gudang_tujuan_updates_container_and_does_not_repeat_history(): void
    {
        $asalId = DB::table('gudangs')->insertGetId(['nama_gudang' => 'Gudang A', 'status' => 'aktif']);
        $tujuanId = DB::table('gudangs')->insertGetId(['nama_gudang' => 'Gudang B', 'status' => 'aktif']);
        DB::table('kontainers')->insert(['nomor_seri_gabungan' => 'ABCD1234567', 'status' => 'active', 'gudangs_id' => $asalId]);
        DB::table('stock_kontainers')->insert(['nomor_seri_gabungan' => 'ABCD1234567', 'status' => 'inactive', 'gudangs_id' => $asalId]);
        $id = DB::table('tanda_terimas')->insertGetId(['no_surat_jalan' => 'SJ-003', 'no_kontainer' => 'ABCD1234567']);
        $request = Request::create('/approval-tanda-terima-pelabuhan/'.$id, 'PATCH', [
            'tanggal' => '2026-10-26',
            'tanggal_checkpoint_supir' => '2026-10-25',
            'tidak_lembur_nginap' => '1',
            'gudang_id' => $tujuanId,
        ]);
        $request->setUserResolver(fn () => (object) ['id' => 7]);

        $controller = new ApprovalTandaTerimaPelabuhanController;
        $controller->approve($request, TandaTerima::findOrFail($id));
        $controller->approve($request, TandaTerima::findOrFail($id));

        $this->assertDatabaseHas('kontainers', ['nomor_seri_gabungan' => 'ABCD1234567', 'gudangs_id' => $tujuanId]);
        $this->assertDatabaseHas('stock_kontainers', ['nomor_seri_gabungan' => 'ABCD1234567', 'gudangs_id' => $tujuanId]);
        $this->assertDatabaseHas('history_kontainers', ['nomor_kontainer' => 'ABCD1234567', 'asal_gudang_id' => $asalId, 'gudang_id' => $tujuanId]);
        $this->assertSame(1, DB::table('history_kontainers')->count());
    }

    public function test_pending_list_only_contains_receipts_waiting_for_port_approval(): void
    {
        DB::table('tanda_terimas')->insert([
            ['no_surat_jalan' => 'SJ-PENDING', 'approval_pelabuhan_status' => 'pending'],
            ['no_surat_jalan' => 'SJ-APPROVED', 'approval_pelabuhan_status' => 'approved'],
        ]);

        $view = (new ApprovalTandaTerimaPelabuhanController)->index(Request::create('/approval-tanda-terima-pelabuhan', 'GET'));

        $this->assertSame(['SJ-PENDING'], $view->getData()['tandaTerimas']->pluck('no_surat_jalan')->all());

        $approvedView = (new ApprovalTandaTerimaPelabuhanController)->index(
            Request::create('/approval-tanda-terima-pelabuhan?status=approved', 'GET')
        );

        $this->assertSame(['SJ-APPROVED'], $approvedView->getData()['tandaTerimas']->pluck('no_surat_jalan')->all());
    }

    public function test_existing_receipts_with_both_dates_are_marked_approved(): void
    {
        DB::table('tanda_terimas')->insert([
            ['no_surat_jalan' => 'SJ-COMPLETE', 'tanggal' => '2026-10-25', 'tanggal_checkpoint_supir' => '2026-10-24'],
            ['no_surat_jalan' => 'SJ-NO-CHECKPOINT', 'tanggal' => '2026-10-25', 'tanggal_checkpoint_supir' => null],
            ['no_surat_jalan' => 'SJ-NO-RECEIPT-DATE', 'tanggal' => null, 'tanggal_checkpoint_supir' => '2026-10-24'],
        ]);

        $migration = require database_path('migrations/2026_10_26_000003_backfill_approval_pelabuhan_status.php');
        $migration->up();

        $this->assertDatabaseHas('tanda_terimas', ['no_surat_jalan' => 'SJ-COMPLETE', 'approval_pelabuhan_status' => 'approved']);
        $this->assertDatabaseHas('tanda_terimas', ['no_surat_jalan' => 'SJ-NO-CHECKPOINT', 'approval_pelabuhan_status' => 'pending']);
        $this->assertDatabaseHas('tanda_terimas', ['no_surat_jalan' => 'SJ-NO-RECEIPT-DATE', 'approval_pelabuhan_status' => 'pending']);
    }
}
