<?php

namespace Tests\Feature;

use App\Http\Controllers\ManifestController;
use App\Http\Controllers\ObController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ObManifestSizeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        foreach (['bls', 'naik_kapal', 'manifests'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                foreach (['nomor_kontainer', 'nama_kapal', 'no_voyage', 'size_kontainer', 'tipe_kontainer'] as $column) {
                    $table->string($column)->nullable();
                }
                $table->timestamps();
            });
        }
        foreach (['kontainers', 'stock_kontainers'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                $table->string('nomor_seri_gabungan');
                $table->string('ukuran');
            });
        }
        DB::table('stock_kontainers')->insert(['nomor_seri_gabungan' => 'AYPU2101376', 'ukuran' => '20']);
    }

    public function test_manifest_update_corrects_existing_size_and_preserves_other_voyages(): void
    {
        $this->seedRecords();
        $response = Model::withoutEvents(fn () => app(ManifestController::class)->autoUpdateSize($this->request()));
        $this->assertTrue($response->getData()->success);
        $this->assertSame('20', DB::table('manifests')->where('no_voyage', 'SR21JB26')->value('size_kontainer'));
        $this->assertSame('40ft', DB::table('manifests')->where('no_voyage', 'OTHER')->value('size_kontainer'));
    }

    public function test_display_uses_current_master_size_instead_of_stored_size(): void
    {
        $manifest = new \App\Models\Manifest([
            'nomor_kontainer' => 'AYPU 210137-6', 'tipe_kontainer' => 'FCL Booking', 'size_kontainer' => '40ft',
        ]);
        $this->assertSame('20', $manifest->master_size);
        DB::table('stock_kontainers')->update(['ukuran' => '40']);
        $this->assertSame('40', $manifest->master_size);
        $this->assertSame('40ft', $manifest->size_kontainer);
    }

    public function test_ob_update_repairs_manifest_even_when_ob_is_already_correct(): void
    {
        $this->seedRecords();
        DB::table('naik_kapal')->insert([
            'nomor_kontainer' => 'AYPU2101376', 'nama_kapal' => 'KM. SRIWIJAYA RAYA',
            'no_voyage' => 'SR21JB26', 'size_kontainer' => '20', 'tipe_kontainer' => 'FCL Booking',
        ]);
        $response = Model::withoutEvents(fn () => app(ObController::class)->confirmUpdateSize($this->request()));
        $this->assertTrue($response->getData()->success);
        $this->assertSame('20', DB::table('manifests')->where('no_voyage', 'SR21JB26')->value('size_kontainer'));
        $this->assertSame('40ft', DB::table('manifests')->where('no_voyage', 'OTHER')->value('size_kontainer'));
    }

    public function test_perincian_counts_follow_master_instead_of_old_manifest_sizes(): void
    {
        $manifests = collect();
        for ($i = 0; $i < 90; $i++) {
            $nomor = $i === 0 ? 'AYPU2101376' : 'TEST'.$i;
            $size = $i < 84 ? '20' : '40';
            if ($i > 0) {
                DB::table('stock_kontainers')->insert(['nomor_seri_gabungan' => $nomor, 'ukuran' => $size.'ft']);
            }
            $manifests->push(new \App\Models\Manifest([
                'nomor_kontainer' => $nomor, 'tipe_kontainer' => 'FCL Booking',
                'size_kontainer' => $i === 0 ? '40ft' : $size,
                'nama_barang' => 'Barang',
            ]));
        }
        $method = new \ReflectionMethod(\App\Http\Controllers\BlController::class, 'buildRekapBongkaranPerincianItems');
        $items = $method->invoke(app(\App\Http\Controllers\BlController::class), $manifests)->keyBy('nama_barang');

        $this->assertSame(84, $items['Container Full 20 feet']['kuantitas']);
        $this->assertSame(6, $items['Container Full 40 feet']['kuantitas']);
        $this->assertSame('40ft', $manifests->first()->size_kontainer);
    }

    private function request(): Request
    {
        return new Request(['nama_kapal' => 'KM. SRIWIJAYA RAYA', 'no_voyage' => 'SR21JB26']);
    }

    private function seedRecords(): void
    {
        foreach (['SR21JB26', 'OTHER'] as $voyage) {
            DB::table('manifests')->insert([
                'nomor_kontainer' => 'AYPU2101376', 'nama_kapal' => 'KM. SRIWIJAYA RAYA',
                'no_voyage' => $voyage, 'size_kontainer' => '40ft', 'tipe_kontainer' => 'FCL Booking',
            ]);
        }
    }
}
