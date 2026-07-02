<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\PricelistPelindo;
use App\Models\TagihanPelindo;
use App\Models\TagihanPelindoItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagihanPelindoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Register default permissions in database
        Permission::firstOrCreate(['name' => 'tagihan-pelindo-view'], ['description' => 'View']);
        Permission::firstOrCreate(['name' => 'tagihan-pelindo-create'], ['description' => 'Create']);
        Permission::firstOrCreate(['name' => 'tagihan-pelindo-edit'], ['description' => 'Edit']);
        Permission::firstOrCreate(['name' => 'tagihan-pelindo-delete'], ['description' => 'Delete']);

        // Define Gates for testing env since AppServiceProvider boots before migrations
        \Illuminate\Support\Facades\Gate::define('tagihan-pelindo-view', function ($user) {
            return $user->permissions()->where('name', 'tagihan-pelindo-view')->exists();
        });
        \Illuminate\Support\Facades\Gate::define('tagihan-pelindo-create', function ($user) {
            return $user->permissions()->where('name', 'tagihan-pelindo-create')->exists();
        });
        \Illuminate\Support\Facades\Gate::define('tagihan-pelindo-edit', function ($user) {
            return $user->permissions()->where('name', 'tagihan-pelindo-edit')->exists();
        });
        \Illuminate\Support\Facades\Gate::define('tagihan-pelindo-delete', function ($user) {
            return $user->permissions()->where('name', 'tagihan-pelindo-delete')->exists();
        });
    }

    public function test_user_without_permission_cannot_view_index()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('tagihan-pelindo.index'));
        $response->assertStatus(403);
    }

    public function test_user_with_permission_can_view_index()
    {
        $user = User::factory()->create();
        $user->permissions()->attach(Permission::where('name', 'tagihan-pelindo-view')->first()->id);
        $this->actingAs($user);

        $response = $this->get(route('tagihan-pelindo.index'));
        $response->assertStatus(200);
    }

    public function test_store_tagihan_pelindo_with_items()
    {
        $user = User::factory()->create();
        $user->permissions()->attach(Permission::where('name', 'tagihan-pelindo-create')->first()->id);
        $this->actingAs($user);

        $pricelist = PricelistPelindo::create([
            'kegiatan' => 'LOLOS KAN KONTEN',
            'tarif' => 150000,
            'status' => 'aktif',
        ]);

        $data = [
            'nomor_tagihan' => 'TPL-20260603-0001',
            'tanggal_tagihan' => '2026-06-03',
            'status_pembayaran' => 'Belum Lunas',
            'keterangan' => 'Tagihan Bulan Juni',
            'items' => [
                [
                    'nomor_kontainer' => 'MSKU1234567',
                    'pricelist_pelindo_id' => $pricelist->id,
                    'kegiatan' => 'LOLOS KAN KONTEN',
                    'ukuran' => '20',
                    'status_kontainer' => 'Full',
                    'tarif' => 150000,
                    'jumlah' => 2,
                    'keterangan' => 'Catatan item 1',
                ],
            ],
        ];

        $response = $this->post(route('tagihan-pelindo.store'), $data);
        $response->assertRedirect(route('tagihan-pelindo.index'));

        $this->assertDatabaseHas('tagihan_pelindos', [
            'nomor_tagihan' => 'TPL-20260603-0001',
            'total_tagihan' => 300000,
            'status_pembayaran' => 'Belum Lunas',
        ]);

        $this->assertDatabaseHas('tagihan_pelindo_items', [
            'nomor_kontainer' => 'MSKU1234567',
            'status_kontainer' => 'Full',
            'tarif' => 150000,
            'jumlah' => 2,
            'total' => 300000,
        ]);
    }

    public function test_update_tagihan_pelindo_replaces_items()
    {
        $user = User::factory()->create();
        $user->permissions()->attach(Permission::where('name', 'tagihan-pelindo-edit')->first()->id);
        $this->actingAs($user);

        $pricelist = PricelistPelindo::create([
            'kegiatan' => 'LOLOS KAN KONTEN',
            'tarif' => 150000,
            'status' => 'aktif',
        ]);

        $tagihan = TagihanPelindo::create([
            'nomor_tagihan' => 'TPL-20260603-0001',
            'tanggal_tagihan' => '2026-06-03',
            'status_pembayaran' => 'Belum Lunas',
            'total_tagihan' => 150000,
            'created_by' => $user->id,
        ]);

        $item = TagihanPelindoItem::create([
            'tagihan_pelindo_id' => $tagihan->id,
            'nomor_kontainer' => 'MSKU1234567',
            'pricelist_pelindo_id' => $pricelist->id,
            'kegiatan' => 'LOLOS KAN KONTEN',
            'ukuran' => '20',
            'tarif' => 150000,
            'jumlah' => 1,
            'total' => 150000,
        ]);

        $data = [
            'nomor_tagihan' => 'TPL-20260603-0001-REV',
            'tanggal_tagihan' => '2026-06-03',
            'status_pembayaran' => 'Lunas',
            'tanggal_bayar' => '2026-06-04',
            'keterangan' => 'Tagihan Bulan Juni Direvisi',
            'items' => [
                [
                    'nomor_kontainer' => 'MSKU7654321',
                    'pricelist_pelindo_id' => $pricelist->id,
                    'kegiatan' => 'LOLOS KAN KONTEN NEW',
                    'ukuran' => '40',
                    'status_kontainer' => 'Empty',
                    'tarif' => 200000,
                    'jumlah' => 3,
                    'keterangan' => 'Catatan revisi',
                ],
            ],
        ];

        $response = $this->put(route('tagihan-pelindo.update', $tagihan->id), $data);
        $response->assertRedirect(route('tagihan-pelindo.index'));

        $this->assertDatabaseHas('tagihan_pelindos', [
            'id' => $tagihan->id,
            'nomor_tagihan' => 'TPL-20260603-0001-REV',
            'total_tagihan' => 600000,
            'status_pembayaran' => 'Lunas',
            'tanggal_bayar' => '2026-06-04',
        ]);

        // Old item should be deleted
        $this->assertDatabaseMissing('tagihan_pelindo_items', [
            'id' => $item->id,
        ]);

        // New item should exist
        $this->assertDatabaseHas('tagihan_pelindo_items', [
            'tagihan_pelindo_id' => $tagihan->id,
            'nomor_kontainer' => 'MSKU7654321',
            'status_kontainer' => 'Empty',
            'kegiatan' => 'LOLOS KAN KONTEN NEW',
            'tarif' => 200000,
            'jumlah' => 3,
            'total' => 600000,
        ]);
    }

    public function test_delete_tagihan_pelindo_cascades_items()
    {
        $user = User::factory()->create();
        $user->permissions()->attach(Permission::where('name', 'tagihan-pelindo-delete')->first()->id);
        $this->actingAs($user);

        $tagihan = TagihanPelindo::create([
            'nomor_tagihan' => 'TPL-20260603-0001',
            'tanggal_tagihan' => '2026-06-03',
            'status_pembayaran' => 'Belum Lunas',
            'total_tagihan' => 150000,
            'created_by' => $user->id,
        ]);

        $item = TagihanPelindoItem::create([
            'tagihan_pelindo_id' => $tagihan->id,
            'nomor_kontainer' => 'MSKU1234567',
            'kegiatan' => 'LOLOS KAN KONTEN',
            'tarif' => 150000,
            'jumlah' => 1,
            'total' => 150000,
        ]);

        $response = $this->delete(route('tagihan-pelindo.destroy', $tagihan->id));
        $response->assertRedirect(route('tagihan-pelindo.index'));

        $this->assertDatabaseMissing('tagihan_pelindos', [
            'id' => $tagihan->id,
        ]);

        $this->assertDatabaseMissing('tagihan_pelindo_items', [
            'id' => $item->id,
        ]);
    }
}
