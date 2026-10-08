<?php

namespace Tests\Feature;

use App\Models\BiayaKapal;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RekapBiayaKapalTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create the necessary permission for viewing biaya kapal
        $permission = Permission::create([
            'name' => 'biaya-kapal-view',
            'description' => 'Test biaya-kapal-view',
        ]);

        // Create user and attach permission
        $this->user = User::factory()->create();
        $this->user->permissions()->attach($permission->id);

        // Seed KlasifikasiBiaya for FK constraint
        \App\Models\KlasifikasiBiaya::create([
            'kode' => 'KB001',
            'nama' => 'Test Biaya Kapal',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_display_rekap_selection_page()
    {
        $this->actingAs($this->user);

        // Seed some biaya kapal records
        BiayaKapal::create([
            'tanggal' => '2026-06-11',
            'nomor_invoice' => 'INV-001',
            'nama_kapal' => ['Sinar Batam'],
            'no_voyage' => ['V-101'],
            'jenis_biaya' => 'KB001',
            'nominal' => 1000000,
            'ppn' => 110000,
            'pph' => 20000,
            'total_biaya' => 1090000,
        ]);

        $response = $this->get(route('rekap-biaya-kapal.index'));

        $response->assertStatus(200);
        $response->assertSee('Rekap Biaya Kapal &amp; Voyage');
        $response->assertSee('Sinar Batam');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_fetch_voyages_via_ajax()
    {
        $this->actingAs($this->user);

        BiayaKapal::create([
            'tanggal' => '2026-06-11',
            'nomor_invoice' => 'INV-001',
            'nama_kapal' => ['Sinar Batam'],
            'no_voyage' => ['V-101'],
            'jenis_biaya' => 'KB001',
            'nominal' => 1000000,
            'ppn' => 110000,
            'pph' => 20000,
            'total_biaya' => 1090000,
        ]);

        $response = $this->getJson(route('rekap-biaya-kapal.get-voyages', ['kapal' => 'Sinar Batam']));

        $response->assertStatus(200);
        $response->assertJson(['V-101']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_show_detailed_rekap_for_ship_and_voyage()
    {
        $this->actingAs($this->user);

        BiayaKapal::create([
            'tanggal' => '2026-06-11',
            'nomor_invoice' => 'INV-001',
            'nama_kapal' => ['Sinar Batam'],
            'no_voyage' => ['V-101'],
            'jenis_biaya' => 'KB001',
            'nominal' => 1000000,
            'ppn' => 110000,
            'pph' => 20000,
            'total_biaya' => 1090000,
            'keterangan' => 'Sewa dermaga',
        ]);

        $response = $this->get(route('rekap-biaya-kapal.show', [
            'kapal' => 'Sinar Batam',
            'voyage' => 'V-101',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Sinar Batam');
        $response->assertSee('V-101');
        $response->assertSee('INV-001');
        $response->assertSee('Sewa dermaga');
        $response->assertSee('Rp 1.000.000');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_shows_batam_labor_totals_from_batam_details()
    {
        $this->actingAs($this->user);
        $invoice = BiayaKapal::create([
            'tanggal' => '2026-10-03', 'nomor_invoice' => 'BKP-10-26-000016',
            'nama_kapal' => ['KM JALESMAS'], 'no_voyage' => ['JALESMAS59'],
            'jenis_biaya' => 'KB001', 'nominal' => 0, 'pph' => 0, 'total_biaya' => 0,
        ]);
        \App\Models\BiayaKapalBuruhBatam::create([
            'biaya_kapal_id' => $invoice->id, 'kapal' => 'KM JALESMAS', 'voyage' => 'JALESMAS59',
            'nominal' => 1000000, 'adjustment' => 50000, 'pph_percent' => 2.5,
            'pph_amount' => 26250, 'total_nominal' => 1023750,
        ]);

        $response = $this->get(route('rekap-biaya-kapal.show', ['kapal' => 'KM JALESMAS', 'voyage' => 'JALESMAS59']));

        $response->assertOk();
        $response->assertSee('BURUH BONGKAR BATAM');
        $response->assertSee('Rp 1.023.750');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function claim_totals_come_from_details_for_the_selected_ship_and_voyage()
    {
        $this->actingAs($this->user);
        $invoice = BiayaKapal::create([
            'tanggal' => '2026-10-03', 'nomor_invoice' => 'BKP-10-26-000018',
            'nama_kapal' => ['Kapal Lain'], 'no_voyage' => ['V99'],
            'jenis_biaya' => 'KB001', 'nominal' => 9000000, 'total_biaya' => 0,
        ]);
        foreach ([['KM JALESMAS', 'JALESMAS59', 1500000], ['KM JALESMAS', 'JALESMAS60', 2500000], ['Kapal Lain', 'JALESMAS59', 5000000]] as [$ship, $voyage, $amount]) {
            $invoice->klaimDetails()->create([
                'kapal' => $ship, 'voyage' => $voyage,
                'subtotal' => $amount, 'total_biaya' => $amount,
            ]);
        }

        $this->get(route('rekap-biaya-kapal.show', ['kapal' => 'KM JALESMAS', 'voyage' => 'JALESMAS59']))
            ->assertOk()->assertSee('BKP-10-26-000018')->assertSee('Rp 1.500.000')
            ->assertViewHas('summary', fn ($summary) => (float) $summary['grand_total'] === 1500000.0
                && (float) $summary['total_nominal'] === 1500000.0);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function claim_rekap_recovers_zero_totals_from_subtotal_and_container_fees()
    {
        $this->actingAs($this->user);
        $invoice = BiayaKapal::create([
            'tanggal' => '2026-10-03', 'nomor_invoice' => 'INV-KLAIM-ZERO-TOTAL',
            'nama_kapal' => ['KM JALESMAS'], 'no_voyage' => ['JALESMAS59'],
            'jenis_biaya' => 'KB001', 'nominal' => 0, 'total_biaya' => 0,
        ]);
        $invoice->klaimDetails()->create([
            'kapal' => 'KM JALESMAS', 'voyage' => 'JALESMAS59',
            'subtotal' => 1500000, 'total_biaya' => 0,
        ]);
        $invoice->klaimDetails()->create([
            'kapal' => 'KM JALESMAS', 'voyage' => 'JALESMAS59',
            'subtotal' => 0, 'total_biaya' => 0,
            'kontainer_ids' => [['biaya_klaim' => 50000], ['biaya_klaim' => 100000]],
        ]);
        $invoice->klaimDetails()->create([
            'kapal' => 'KM JALESMAS', 'voyage' => 'JALESMAS60',
            'subtotal' => 0, 'total_biaya' => 0,
            'kontainer_ids' => [['biaya_klaim' => 9000000]],
        ]);

        $this->get(route('rekap-biaya-kapal.show', ['kapal' => 'KM JALESMAS', 'voyage' => 'JALESMAS59']))
            ->assertOk()->assertSee('Rp 1.650.000')
            ->assertViewHas('summary', fn ($summary) => (float) $summary['grand_total'] === 1650000.0);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function legacy_claim_without_details_uses_parent_nominal_when_total_is_zero()
    {
        $this->actingAs($this->user);
        \App\Models\KlasifikasiBiaya::create(['kode' => 'KB002', 'nama' => 'Klaim']);
        BiayaKapal::create([
            'tanggal' => '2026-10-03', 'nomor_invoice' => 'INV-KLAIM-LEGACY',
            'nama_kapal' => ['KM JALESMAS'], 'no_voyage' => ['JALESMAS59'],
            'jenis_biaya' => 'KB002', 'nominal' => 1500000, 'ppn' => 0, 'pph' => 25000, 'total_biaya' => 0,
        ]);

        $this->get(route('rekap-biaya-kapal.show', ['kapal' => 'KM JALESMAS', 'voyage' => 'JALESMAS59']))
            ->assertOk()->assertSee('Rp 1.475.000')
            ->assertViewHas('summary', fn ($summary) => (float) $summary['grand_total'] === 1475000.0);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function temas_rekap_counts_invoices_on_target_voyages_without_counting_dp_twice()
    {
        $this->actingAs($this->user);
        $create = function ($number, $ship, $voyage, $mode, $tagihan, $cash, $used) {
            $invoice = BiayaKapal::create([
                'tanggal' => '2026-10-08', 'nomor_invoice' => $number,
                'nama_kapal' => [$ship], 'no_voyage' => [$voyage],
                'jenis_biaya' => 'KB001', 'nominal' => $cash, 'total_biaya' => $cash,
            ]);
            $stage = \App\Models\BiayaKapalTemasStage::create([
                'biaya_kapal_id' => $invoice->id,
                'kapal' => $ship, 'voyage' => $voyage, 'payment_mode' => $mode,
                'nilai_tagihan' => $tagihan, 'nominal_dibayar' => $cash, 'dp_diperhitungkan' => $used,
            ]);
            $stage->details()->create([
                'biaya_kapal_id' => $invoice->id, 'kapal' => $ship, 'voyage' => $voyage,
                'nomor_bl' => $mode === 'dp' ? null : '01',
                'jenis_biaya' => $mode === 'dp' ? 'DP / Uang Muka TEMAS' : 'Freight',
                'kuantitas' => 1, 'harga' => $tagihan, 'sub_total' => $tagihan, 'grand_total' => $cash,
            ]);

            return $invoice;
        };
        $dp = $create('INV-DP-35JT', 'Kapal A', 'V01', 'dp', 35000000, 35000000, 0);
        $first = $create('INV-TAGIHAN-33JT', 'Kapal A', 'V01', 'pelunasan_dp', 33000000, 0, 33000000);
        $second = $create('INV-TAGIHAN-5JT', 'Kapal B', 'V02', 'pelunasan_dp', 5000000, 3000000, 2000000);

        $this->get(route('rekap-biaya-kapal.show', ['kapal' => 'Kapal A', 'voyage' => 'V01']))
            ->assertOk()
            ->assertViewHas('summary', fn ($summary) => (float) $summary['grand_total'] === 33000000.0)
            ->assertViewHas('biayaKapals', fn ($items) => $items->contains('id', $first->id) && ! $items->contains('id', $dp->id))
            ->assertSee('Rp 33.000.000')->assertDontSee('Rp 35.000.000');
        $this->get(route('rekap-biaya-kapal.show', ['kapal' => 'Kapal B', 'voyage' => 'V02', 'bl' => '01']))
            ->assertOk()
            ->assertViewHas('summary', fn ($summary) => (float) $summary['grand_total'] === 5000000.0)
            ->assertSee('Rp 5.000.000');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function bl_filter_applies_only_to_temas_and_keeps_other_costs()
    {
        $this->actingAs($this->user);
        $invoice = BiayaKapal::create([
            'tanggal' => '2026-10-08', 'nomor_invoice' => 'INV-BL-01',
            'nama_kapal' => ['Sinar Batam'], 'no_voyage' => ['V101', 'V102'],
            'jenis_biaya' => 'KB001', 'nominal' => 8500, 'total_biaya' => 8500,
        ]);
        foreach ([['01-1', 'V101', 1000], ['01-2', 'V101', 2000], ['02', 'V101', 5000], ['03', 'V102', 500]] as [$bl, $voyage, $amount]) {
            $invoice->temasDetails()->create([
                'kapal' => 'Sinar Batam', 'voyage' => $voyage, 'nomor_bl' => $bl,
                'jenis_biaya' => 'Freight', 'kuantitas' => 1, 'harga' => $amount,
                'sub_total' => $amount, 'grand_total' => $amount,
            ]);
        }
        $shared = BiayaKapal::create([
            'tanggal' => '2026-10-08', 'nomor_invoice' => 'INV-NO-BL',
            'nama_kapal' => ['Sinar Batam'], 'no_voyage' => ['V101'],
            'jenis_biaya' => 'KB001', 'nominal' => 9000, 'total_biaya' => 9000,
        ]);
        $this->getJson(route('rekap-biaya-kapal.get-bls', ['kapal' => 'Sinar Batam', 'voyage' => 'V101']))
            ->assertOk()->assertExactJson(['01', '02']);

        $response = $this->get(route('rekap-biaya-kapal.show', ['kapal' => 'Sinar Batam', 'voyage' => 'V101', 'bl' => '01']));
        $response->assertOk();
        $response->assertViewHas('summary', fn ($summary) => (float) $summary['grand_total'] === 12000.0);
        $response->assertViewHas('biayaKapals', fn ($items) => $items->contains('id', $invoice->id) && $items->contains('id', $shared->id));
        $response->assertDontSee('BL: 02');

        $unfiltered = $this->get(route('rekap-biaya-kapal.show', ['kapal' => 'Sinar Batam', 'voyage' => 'V101']));
        $unfiltered->assertOk()->assertViewHas('summary', fn ($summary) => (float) $summary['grand_total'] === 17000.0);
    }
}
