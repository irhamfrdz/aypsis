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
}
