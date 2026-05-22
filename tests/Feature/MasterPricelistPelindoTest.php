<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\PricelistPelindo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterPricelistPelindoTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected $permissions = [];

    protected function setUp(): void
    {
        parent::setUp();

        // Create the permissions
        $perms = [
            'master-pricelist-pelindo-view',
            'master-pricelist-pelindo-create',
            'master-pricelist-pelindo-update',
            'master-pricelist-pelindo-delete',
        ];

        foreach ($perms as $pName) {
            $this->permissions[$pName] = Permission::create([
                'name' => $pName,
                'description' => 'Test '.$pName,
            ]);
        }

        // Create user and attach permissions
        $this->user = User::factory()->create();
        foreach ($this->permissions as $p) {
            $this->user->permissions()->attach($p->id);
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_display_pricelist_pelindo_index()
    {
        $this->actingAs($this->user);

        PricelistPelindo::create([
            'kegiatan' => 'Sewa Crane',
            'ukuran' => '20 Feet',
            'tarif' => 1500000,
            'keterangan' => 'Keterangan test',
            'status' => 'aktif',
        ]);

        $response = $this->get(route('master.pricelist-pelindo.index'));

        $response->assertStatus(200);
        $response->assertSee('Sewa Crane');
        $response->assertSee('20 Feet');
        $response->assertSee('Rp 1.500.000');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_store_pricelist_pelindo()
    {
        $this->actingAs($this->user);

        $data = [
            'kegiatan' => 'Jasa Labuh',
            'ukuran' => '40 Feet',
            'tarif' => '2.500.000', // Formatted input
            'keterangan' => 'Keterangan store',
            'status' => 'aktif',
        ];

        $response = $this->post(route('master.pricelist-pelindo.store'), $data);

        $response->assertRedirect(route('master.pricelist-pelindo.index'));
        $this->assertDatabaseHas('pricelist_pelindos', [
            'kegiatan' => 'Jasa Labuh',
            'ukuran' => '40 Feet',
            'tarif' => 2500000.00,
            'keterangan' => 'Keterangan store',
            'status' => 'aktif',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_update_pricelist_pelindo()
    {
        $this->actingAs($this->user);

        $pricelist = PricelistPelindo::create([
            'kegiatan' => 'Sewa Forklift',
            'ukuran' => '10 Ton',
            'tarif' => 750000,
            'keterangan' => 'Keterangan awal',
            'status' => 'aktif',
        ]);

        $data = [
            'kegiatan' => 'Sewa Forklift Updated',
            'ukuran' => '12 Ton',
            'tarif' => '850.000', // Formatted input
            'keterangan' => 'Keterangan updated',
            'status' => 'nonaktif',
        ];

        $response = $this->put(route('master.pricelist-pelindo.update', $pricelist->id), $data);

        $response->assertRedirect(route('master.pricelist-pelindo.index'));
        $this->assertDatabaseHas('pricelist_pelindos', [
            'id' => $pricelist->id,
            'kegiatan' => 'Sewa Forklift Updated',
            'ukuran' => '12 Ton',
            'tarif' => 850000.00,
            'keterangan' => 'Keterangan updated',
            'status' => 'nonaktif',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_delete_pricelist_pelindo()
    {
        $this->actingAs($this->user);

        $pricelist = PricelistPelindo::create([
            'kegiatan' => 'Sewa Reach Stacker',
            'ukuran' => '45 Feet',
            'tarif' => 3000000,
            'keterangan' => 'Keterangan delete',
            'status' => 'aktif',
        ]);

        $response = $this->delete(route('master.pricelist-pelindo.destroy', $pricelist->id));

        $response->assertRedirect(route('master.pricelist-pelindo.index'));
        $this->assertDatabaseMissing('pricelist_pelindos', [
            'id' => $pricelist->id,
        ]);
    }
}
