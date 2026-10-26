<?php

namespace Tests\Feature;

use App\Models\BiayaKapal;
use App\Models\BiayaKapalMeratus;
use App\Models\BiayaKapalMeratusStage;
use App\Services\MeratusBillingService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class MeratusBillingTest extends TestCase
{
    private $dispatcher;

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'database.connections.sqlite.foreign_key_constraints' => true]);
        DB::purge('sqlite');
        $this->dispatcher = Model::getEventDispatcher();
        Model::unsetEventDispatcher();

        Schema::create('biaya_kapals', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->nullable();
            $table->json('nama_kapal')->nullable();
            $table->json('no_voyage')->nullable();
            $table->decimal('nominal', 15, 2)->default(0);
            $table->decimal('total_biaya', 15, 2)->default(0);
            $table->decimal('sisa_pembayaran', 15, 2)->default(0);
            $table->string('status_pembayaran')->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('biaya_kapal_meratus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('biaya_kapal_id');
            $table->string('kapal')->nullable();
            $table->string('voyage')->nullable();
            $table->string('jenis_biaya')->nullable();
            $table->string('nomor_kontainer')->nullable();
            $table->string('nomor_referensi')->nullable();
            $table->unsignedBigInteger('bl_id')->nullable();
            $table->unsignedBigInteger('pricelist_meratus_id')->nullable();
            $table->string('lokasi')->nullable();
            $table->string('size')->nullable();
            foreach (['kuantitas', 'harga', 'sub_total', 'pph', 'ppn', 'biaya_materai', 'adjustment', 'biaya_admin', 'grand_total'] as $field) {
                $table->decimal($field, 15, 2)->default(0);
            }
            $table->boolean('pph_active')->default(false);
            $table->boolean('ppn_active')->default(false);
            $table->boolean('is_muat')->default(false);
            $table->boolean('is_bongkar')->default(false);
            $table->boolean('is_storage')->default(false);
            $table->date('tanggal_mulai_storage')->nullable();
            $table->date('tanggal_selesai_storage')->nullable();
            $table->string('penerima')->nullable();
            $table->string('nomor_rekening')->nullable();
            $table->date('tanggal_invoice_vendor')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
        (require database_path('migrations/2026_10_24_090000_add_meratus_billing_stages.php'))->up();
        Schema::create('pembayaran_biaya_kapals', function (Blueprint $table) {
            $table->id();
            $table->string('status_pembayaran');
            $table->softDeletes();
        });
        Schema::create('pembayaran_biaya_kapal_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('biaya_kapal_id');
            $table->foreignId('pembayaran_biaya_kapal_id');
        });
    }

    protected function tearDown(): void
    {
        Model::setEventDispatcher($this->dispatcher);
        parent::tearDown();
    }

    private function rows(): array
    {
        return [
            'kapal' => 'KM TEST', 'voyage' => 'V001', 'payment_mode' => 'lunas',
            'types' => ['MANUAL', 'MANUAL'],
            'manual_names' => ['Biaya Kontainer', 'ADM DO'],
            'custom_prices' => [100, 25], 'quantities' => [2, 1],
            'nomor_kontainers' => ['A123,B456', 'A123,B456'],
            'nomor_bls' => ['BL001', 'BL001'],
            'size_items' => ['20ft', '20ft'],
            'per_containers' => [1, 0],
        ];
    }

    public function test_direct_bill_uses_container_count_and_single_bl_charge(): void
    {
        $invoice = BiayaKapal::create(['tanggal' => '2026-10-24']);
        app(MeratusBillingService::class)->replace($invoice, [$this->rows()]);

        $this->assertEquals(225, (float) $invoice->fresh()->nominal);
        $this->assertSame([2.0, 1.0], BiayaKapalMeratus::orderBy('id')->get()->pluck('kuantitas')->map(fn ($n) => (float) $n)->all());
        $this->assertSame(['BL001', 'BL001'], BiayaKapalMeratus::orderBy('id')->pluck('nomor_bl')->all());
        $this->assertEquals(225, (float) BiayaKapalMeratusStage::first()->nilai_tagihan);
    }

    public function test_one_bl_can_have_different_charges_for_each_container(): void
    {
        $invoice = BiayaKapal::create(['tanggal' => '2026-10-24']);
        $section = $this->rows();
        $section['types'] = ['MANUAL', 'MANUAL', 'MANUAL'];
        $section['manual_names'] = ['Handling', 'Handling', 'ADM DO'];
        $section['custom_prices'] = [100, 150, 25];
        $section['quantities'] = [1, 1, 1];
        $section['nomor_kontainers'] = ['A123', 'B456', 'A123,B456'];
        $section['nomor_bls'] = ['BL001', 'BL001', 'BL001'];
        $section['size_items'] = ['20ft', '40ft', '20ft'];
        $section['per_containers'] = [1, 1, 0];

        app(MeratusBillingService::class)->replace($invoice, [$section]);

        $details = BiayaKapalMeratus::orderBy('id')->get();
        $this->assertSame(['A123', 'B456', 'A123,B456'], $details->pluck('nomor_kontainer')->all());
        $this->assertSame([100.0, 150.0, 25.0], $details->pluck('sub_total')->map(fn ($amount) => (float) $amount)->all());
        $this->assertEquals(275, (float) $invoice->fresh()->nominal);
    }

    public function test_dp_settlement_preserves_remaining_balance(): void
    {
        $dpInvoice = BiayaKapal::create(['tanggal' => '2026-10-20']);
        app(MeratusBillingService::class)->replace($dpInvoice, [[
            'kapal' => 'KM TEST', 'voyage' => 'V001', 'payment_mode' => 'dp',
            'nominal_dibayar' => 300, 'tanggal_dp' => '2026-10-20', 'nama_bank' => 'BCA',
        ]]);
        $dp = BiayaKapalMeratusStage::first();

        $settlementInvoice = BiayaKapal::create(['tanggal' => '2026-10-24']);
        $section = $this->rows();
        $section['payment_mode'] = 'pelunasan_dp';
        $section['dp_stage_ids'] = [$dp->id];
        app(MeratusBillingService::class)->replace($settlementInvoice, [$section]);

        $this->assertEquals(225, (float) BiayaKapalMeratusStage::where('biaya_kapal_id', $settlementInvoice->id)->value('nilai_tagihan'));
        $this->assertEquals(0, (float) $settlementInvoice->fresh()->nominal);
        $this->assertSame('paid', $settlementInvoice->fresh()->status_pembayaran);
        $this->assertSame(7500, app(MeratusBillingService::class)->balanceCents($dp));

        $this->expectException(ValidationException::class);
        app(MeratusBillingService::class)->replace($dpInvoice, [[
            'kapal' => 'KM TEST', 'voyage' => 'V001', 'payment_mode' => 'dp',
            'nominal_dibayar' => 200, 'tanggal_dp' => '2026-10-20', 'nama_bank' => 'BCA',
        ]]);
    }
}
