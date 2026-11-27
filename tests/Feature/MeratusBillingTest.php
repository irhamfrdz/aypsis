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
    use \Tests\CreatesSharedShippingDpSchema;

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
        $this->createOtherShippingDpTables('temas');
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

    private function sharedDp(string $source, float $amount)
    {
        $invoice = BiayaKapal::create(['tanggal' => '2026-10-20']);
        $service = $source === 'temas' ? app(\App\Services\TemasBillingService::class) : app(MeratusBillingService::class);
        $service->replace($invoice, [[
            'kapal' => 'KM SOURCE', 'voyage' => 'SOURCE01', 'payment_mode' => 'dp',
            'nominal_dibayar' => $amount, 'tanggal_dp' => '2026-10-20', 'nama_bank' => 'BCA',
        ]]);
        $model = $source === 'temas' ? \App\Models\BiayaKapalTemasStage::class : BiayaKapalMeratusStage::class;

        return $model::where('biaya_kapal_id', $invoice->id)->firstOrFail();
    }

    private function sharedCosts(array $keys, float $amount): array
    {
        return array_replace($this->rows(), [
            'payment_mode' => 'pelunasan_dp', 'dp_stage_ids' => $keys,
            'types' => ['MANUAL'], 'manual_names' => ['Biaya Kontainer'],
            'custom_prices' => [$amount], 'quantities' => [1],
            'nomor_kontainers' => ['A123'], 'nomor_bls' => ['BL001'],
            'size_items' => ['20ft'], 'per_containers' => [1],
        ]);
    }

    public function test_temas_remaining_290000_is_shared_with_meratus_and_restored_on_edit_and_delete(): void
    {
        $pool = app(\App\Services\SharedShippingDpService::class);
        $temas = app(\App\Services\TemasBillingService::class);
        $meratus = app(MeratusBillingService::class);
        $dp = $this->sharedDp('temas', 69000000);
        $previous = BiayaKapal::create(['tanggal' => '2026-10-24']);
        $temas->replace($previous, [$this->sharedCosts([$dp->id], 68710000)]);
        $this->assertSame(29000000, $pool->balanceCents($dp));

        foreach (['Temas' => $temas, 'Meratus' => $meratus] as $name => $service) {
            $response = app(\App\Http\Controllers\BiayaKapalController::class)->{'getOutstanding'.$name.'Dps'}(new \Illuminate\Http\Request, $service)->getData(true);
            $this->assertSame('temas:'.$dp->id, $response['data'][0]['id']);
            $this->assertEquals(290000, $response['data'][0]['saldo_dp']);
        }

        $invoice = BiayaKapal::create(['tanggal' => '2026-10-24']);
        $meratus->replace($invoice, [$this->sharedCosts([$pool->key($dp)], 200000)]);
        $this->assertSame(9000000, $temas->balanceCents($dp));
        $this->assertSame(9000000, $meratus->balanceCents($dp));
        $this->assertSame(29000000, $pool->balanceCents($dp, $invoice->id));
        $stage = BiayaKapalMeratusStage::where('biaya_kapal_id', $invoice->id)->firstOrFail();
        $this->assertNull($stage->dp_stage_id);
        $this->assertCount(1, $stage->sharedDpStages);
        $this->assertEquals(200000, $stage->sharedDpStages->first()->pivot->nominal_digunakan);

        $meratus->replace($invoice, [$this->sharedCosts([$pool->key($dp)], 100000)]);
        $this->assertSame(19000000, $pool->balanceCents($dp));
        $invoice->update(['status_pembayaran' => 'cancelled']);
        $this->assertSame(29000000, $pool->balanceCents($dp));
        $invoice->update(['status_pembayaran' => 'paid']);
        $invoice->delete();
        $this->assertSame(29000000, $pool->balanceCents($dp));
    }

    public function test_shared_dp_supports_both_sources_with_colliding_ids_and_prevents_reuse(): void
    {
        $pool = app(\App\Services\SharedShippingDpService::class);
        $temas = $this->sharedDp('temas', 290000);
        $meratus = $this->sharedDp('meratus', 100000);
        $this->assertSame($temas->id, $meratus->id);
        $invoice = BiayaKapal::create(['tanggal' => '2026-10-24']);
        app(\App\Services\TemasBillingService::class)->replace($invoice, [
            $this->sharedCosts([$pool->key($temas), $pool->key($meratus)], 400000),
        ]);
        $stage = \App\Models\BiayaKapalTemasStage::where('biaya_kapal_id', $invoice->id)->firstOrFail();
        $this->assertCount(2, $stage->sharedDpStages);
        $this->assertEquals(390000, $stage->dp_diperhitungkan);
        $this->assertEquals(10000, $invoice->fresh()->nominal);
        $this->assertSame(0, $pool->balanceCents($temas));
        $this->assertSame(0, $pool->balanceCents($meratus));
        $this->assertCount(0, $pool->available());

        $next = BiayaKapal::create(['tanggal' => '2026-10-24']);
        try {
            app(MeratusBillingService::class)->replace($next, [$this->sharedCosts([$pool->key($temas)], 100000)]);
            $this->fail('An exhausted shared DP must be rejected.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('meratus.0.dp_stage_ids', $e->errors());
        }
        $this->expectException(ValidationException::class);
        app(MeratusBillingService::class)->assertCanReplace($meratus->biayaKapal);
    }

    public function test_shared_source_cannot_be_changed_even_after_cross_settlement_is_deleted(): void
    {
        $dp = $this->sharedDp('temas', 290000);
        $invoice = BiayaKapal::create(['tanggal' => '2026-10-24']);
        app(MeratusBillingService::class)->replace($invoice, [$this->sharedCosts(['temas:'.$dp->id], 200000)]);
        $invoice->delete();
        $this->expectException(ValidationException::class);
        app(\App\Services\TemasBillingService::class)->assertCanReplace($dp->biayaKapal);
    }

    public function test_duplicate_or_invalid_shared_source_rolls_back_without_spending_dp(): void
    {
        $dp = $this->sharedDp('meratus', 290000);
        $invoice = BiayaKapal::create(['tanggal' => '2026-10-24']);
        foreach ([[$dp->id, 'meratus:'.$dp->id], ['temas:9999'], ['other:1']] as $keys) {
            try {
                app(MeratusBillingService::class)->replace($invoice, [$this->sharedCosts($keys, 200000)]);
                $this->fail('Invalid shared DP must be rejected.');
            } catch (ValidationException $e) {
                $this->assertSame(29000000, app(MeratusBillingService::class)->balanceCents($dp));
                $this->assertFalse(BiayaKapalMeratusStage::where('biaya_kapal_id', $invoice->id)->exists());
            }
        }
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
