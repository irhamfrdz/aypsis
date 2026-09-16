<?php

namespace Tests\Feature;

use App\Models\Manifest;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ManifestShipperTest extends TestCase
{
    private Manifest $manifest;

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'cache.default' => 'array']);
        DB::purge('sqlite');
        Schema::create('manifests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shipper_id')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            foreach (['nomor_bl', 'nomor_kontainer', 'nama_barang', 'no_voyage', 'pengirim', 'alamat_pengirim', 'penerima', 'alamat_penerima', 'notify_party', 'alamat_notify_party'] as $field) {
                $table->text($field)->nullable();
            }
            $table->timestamps();
        });
        Schema::create('shipper_consignees', function (Blueprint $table) {
            $table->id();
            foreach (['shipper', 'alamat_shipper', 'consignee', 'notify_party_consignee', 'alamat_notify_party_consignee'] as $field) {
                $table->text($field)->nullable();
            }
        });
        Schema::create('pengirims', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pengirim');
            $table->string('nickname1')->nullable();
        });
        DB::table('shipper_consignees')->insert([
            'id' => 10, 'shipper' => 'PT Pengirim', 'alamat_shipper' => 'Alamat pengirim baru',
            'consignee' => 'PT Penerima', 'notify_party_consignee' => 'Notify baru',
            'alamat_notify_party_consignee' => 'Alamat notify baru',
        ]);
        $this->manifest = Manifest::withoutEvents(fn () => Manifest::create([
            'nomor_bl' => 'BL-001', 'nomor_kontainer' => 'AYPU1234567', 'nama_barang' => 'Barang lama', 'no_voyage' => 'JB-001',
            'pengirim' => 'Pengirim lama', 'alamat_pengirim' => 'Alamat lama', 'penerima' => 'Penerima lama',
            'notify_party' => 'Notify lama', 'alamat_notify_party' => 'Alamat notify lama',
        ]));
        $user = new User;
        $user->setRawAttributes(['id' => 5, 'username' => 'manifest-test']);
        $user->setRelation('permissions', collect([new Permission(['name' => 'manifest-edit'])]));
        $this->actingAs($user);
    }

    private function saveShipper(array $fields = [])
    {
        return $this->postJson(route('report.manifests.update-shipper', $this->manifest->id), array_merge(['shipper_id' => 10], $fields));
    }

    public function test_selection_saves_all_edit_autofill_fields_and_preserves_other_manifest_fields(): void
    {
        DB::table('pengirims')->insert(['nama_pengirim' => 'PT Pengirim', 'nickname1' => 'PENGIRIM']);
        $this->saveShipper(['nomor_bl' => 'CHANGED', 'nama_barang' => 'CHANGED'])->assertOk()
            ->assertJsonPath('manifest.pengirim', 'PENGIRIM')
            ->assertJsonPath('manifest.penerima', 'PT Penerima')
            ->assertJsonPath('manifest.alamat_pengirim', 'Alamat pengirim baru')
            ->assertJsonPath('manifest.notify_party', 'Notify baru')
            ->assertJsonPath('manifest.alamat_notify_party', 'Alamat notify baru');
        $this->assertDatabaseHas('manifests', [
            'id' => $this->manifest->id, 'shipper_id' => 10, 'updated_by' => 5,
            'nomor_bl' => 'BL-001', 'nama_barang' => 'Barang lama', 'no_voyage' => 'JB-001',
        ]);
    }

    public function test_manual_corrections_and_empty_values_are_saved(): void
    {
        $this->saveShipper(['penerima' => 'Penerima dikoreksi', 'notify_party' => null, 'alamat_notify_party' => null])
            ->assertOk()->assertJsonPath('manifest.penerima', 'Penerima dikoreksi')
            ->assertJsonPath('manifest.notify_party', null)->assertJsonPath('manifest.alamat_notify_party', null);
    }

    public function test_empty_master_fields_preserve_existing_values_like_the_edit_form(): void
    {
        DB::table('shipper_consignees')->where('id', 10)->update([
            'alamat_shipper' => null, 'consignee' => null, 'notify_party_consignee' => null, 'alamat_notify_party_consignee' => null,
        ]);
        $this->saveShipper()->assertOk()->assertJsonPath('manifest.penerima', 'Penerima lama')
            ->assertJsonPath('manifest.alamat_pengirim', 'Alamat lama')->assertJsonPath('manifest.notify_party', 'Notify lama');
    }

    public function test_invalid_selection_and_consignee_only_master_cannot_be_saved(): void
    {
        $this->saveShipper(['shipper_id' => 999])->assertUnprocessable();
        $this->saveShipper(['shipper_id' => null])->assertUnprocessable();
        DB::table('shipper_consignees')->where('id', 10)->update(['shipper' => null]);
        $this->saveShipper()->assertUnprocessable()->assertJsonValidationErrors('shipper_id');
        $this->assertNull($this->manifest->fresh()->shipper_id);
    }

    public function test_manifest_edit_permission_is_required(): void
    {
        auth()->user()->setRelation('permissions', collect([new Permission(['name' => 'manifest-view'])]));
        $this->saveShipper()->assertForbidden();
        $this->assertNull($this->manifest->fresh()->shipper_id);
    }

    public function test_search_returns_the_same_master_id_and_autofill_fields_used_by_edit(): void
    {
        $this->getJson('/api/manifests/search-shippers?q=Pengirim')->assertOk()
            ->assertJsonPath('0.real_id', 10)->assertJsonPath('0.text', 'PT Pengirim')
            ->assertJsonPath('0.alamat', 'Alamat pengirim baru')->assertJsonPath('0.consignee', 'PT Penerima')
            ->assertJsonPath('0.notify_party', 'Notify baru')->assertJsonPath('0.alamat_notify_party', 'Alamat notify baru');
    }

    public function test_row_data_and_modal_render_without_injecting_manifest_text(): void
    {
        $this->manifest->pengirim = '<script>alert(1)</script>';
        $html = view('manifests.partials.shipper-button', ['manifest' => $this->manifest])->render();
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertStringContainsString(route('report.manifests.update-shipper', $this->manifest->id), $html);
        $html = view('manifests.partials.shipper-modal')->render();
        foreach (['alamat_pengirim', 'penerima', 'notify_party', 'alamat_notify_party'] as $field) {
            $this->assertStringContainsString('name="'.$field.'"', $html);
        }
    }
}
