<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\MasterPricelistBiayaStorage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterPricelistBiayaStorageTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $permissions = [];

    protected function setUp(): void
    {
        parent::setUp();

        // Create the permissions
        $perms = [
            'master-pricelist-biaya-storage-view',
            'master-pricelist-biaya-storage-create',
            'master-pricelist-biaya-storage-update',
            'master-pricelist-biaya-storage-delete',
        ];

        foreach ($perms as $pName) {
            $this->permissions[$pName] = Permission::create([
                'name' => $pName,
                'description' => 'Test ' . $pName,
            ]);
        }

        // Create user and attach permissions
        $this->user = User::factory()->create();
        foreach ($this->permissions as $p) {
            $this->user->permissions()->attach($p->id);
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_display_edit_form_with_route_model_binding()
    {
        $this->actingAs($this->user);

        $pricelist = MasterPricelistBiayaStorage::create([
            'vendor' => 'Test Vendor',
            'lokasi' => 'Jakarta',
            'size_kontainer' => '20',
            'tarif_massa_1' => 100000,
            'tarif_massa_2' => 150000,
            'status' => 'aktif',
            'keterangan' => 'Test Keterangan',
        ]);

        // Prior to our fix, this edit route or rendering the edit form action would throw:
        // "Missing required parameter for [Route: master-pricelist-biaya-storage.update]"
        // because $masterPricelistBiayaStorage was not bound, returning a blank model with null ID.
        $response = $this->get(route('master-pricelist-biaya-storage.edit', $pricelist->id));

        $response->assertStatus(200);
        $response->assertSee('Edit Tarif Biaya Storage');
        $response->assertSee('Test Vendor');
        // Check that the form action contains the correct update route with the ID
        $response->assertSee(route('master-pricelist-biaya-storage.update', $pricelist->id));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_update_pricelist_biaya_storage()
    {
        $this->actingAs($this->user);

        $pricelist = MasterPricelistBiayaStorage::create([
            'vendor' => 'Test Vendor',
            'lokasi' => 'Jakarta',
            'size_kontainer' => '20',
            'tarif_massa_1' => 100000,
            'tarif_massa_2' => 150000,
            'status' => 'aktif',
            'keterangan' => 'Test Keterangan',
        ]);

        $data = [
            'vendor' => 'Updated Vendor',
            'lokasi' => 'Batam',
            'size_kontainer' => '40',
            'tarif_massa_1' => 200000,
            'tarif_massa_2' => 250000,
            'status' => 'non-aktif',
            'keterangan' => 'Updated Keterangan',
        ];

        $response = $this->put(route('master-pricelist-biaya-storage.update', $pricelist->id), $data);

        $response->assertRedirect(route('master-pricelist-biaya-storage.index'));
        $this->assertDatabaseHas('master_pricelist_biaya_storages', [
            'id' => $pricelist->id,
            'vendor' => 'Updated Vendor',
            'lokasi' => 'Batam',
            'size_kontainer' => '40',
            'tarif_massa_1' => 200000,
            'tarif_massa_2' => 250000,
            'status' => 'non-aktif',
            'keterangan' => 'Updated Keterangan',
        ]);
    }
}
