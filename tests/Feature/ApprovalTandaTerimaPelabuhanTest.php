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
            $table->timestamps();
        });
        Schema::create('tanda_terimas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('surat_jalan_id')->nullable();
            $table->string('no_surat_jalan');
            $table->date('tanggal')->nullable();
            $table->date('tanggal_checkpoint_supir')->nullable();
            $table->string('status')->default('draft');
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
        $suratJalanId = DB::table('surat_jalans')->insertGetId([]);
        $id = DB::table('tanda_terimas')->insertGetId([
            'surat_jalan_id' => $suratJalanId,
            'no_surat_jalan' => 'SJ-001',
        ]);
        $tandaTerima = TandaTerima::findOrFail($id);
        $request = Request::create('/approval-tanda-terima-pelabuhan/'.$id, 'PATCH', [
            'tanggal' => '2026-10-26',
            'tanggal_checkpoint_supir' => '2026-10-25',
        ]);
        $request->setUserResolver(fn () => (object) ['id' => 7]);

        (new ApprovalTandaTerimaPelabuhanController)->approve($request, $tandaTerima);

        $saved = $tandaTerima->fresh();
        $this->assertSame('2026-10-26', $saved->tanggal->format('Y-m-d'));
        $this->assertSame('2026-10-25', $saved->tanggal_checkpoint_supir->format('Y-m-d'));
        $this->assertSame('approved', $saved->status);
        $this->assertSame(7, (int) $saved->updated_by);
        $this->assertDatabaseHas('surat_jalans', [
            'id' => $suratJalanId,
            'tanggal_checkpoint' => '2026-10-25',
        ]);
    }

    public function test_approval_rejects_missing_dates(): void
    {
        $id = DB::table('tanda_terimas')->insertGetId(['no_surat_jalan' => 'SJ-002']);
        $request = Request::create('/approval-tanda-terima-pelabuhan/'.$id, 'PATCH', ['tanggal' => '2026-10-26']);

        $this->expectException(ValidationException::class);
        (new ApprovalTandaTerimaPelabuhanController)->approve($request, TandaTerima::findOrFail($id));
    }

    public function test_pending_list_only_contains_receipts_waiting_for_port_approval(): void
    {
        DB::table('tanda_terimas')->insert([
            ['no_surat_jalan' => 'SJ-DRAFT', 'status' => 'draft'],
            ['no_surat_jalan' => 'SJ-APPROVED', 'status' => 'approved'],
            ['no_surat_jalan' => 'SJ-COMPLETED', 'status' => 'completed'],
        ]);

        $view = (new ApprovalTandaTerimaPelabuhanController)->index(Request::create('/approval-tanda-terima-pelabuhan', 'GET'));

        $this->assertSame(['SJ-DRAFT'], $view->getData()['tandaTerimas']->pluck('no_surat_jalan')->all());
    }
}
