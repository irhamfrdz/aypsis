<?php

namespace Tests\Feature;

use App\Http\Controllers\BiayaKapalController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BiayaKapalPelindoFormParityTest extends TestCase
{
    public function test_create_and_edit_share_pelindo_calculation_and_ship_matching(): void
    {
        Schema::create('klasifikasi_biayas', function (Blueprint $table) {
            $table->id();
            $table->string('kode');
            $table->string('nama');
        });
        Schema::create('manifests', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kapal');
            $table->string('no_voyage');
        });
        Schema::create('pricelist_pelindos', function (Blueprint $table) {
            $table->id();
            $table->string('kegiatan');
            $table->string('lokasi');
            $table->string('status_kontainer')->nullable();
            $table->string('ukuran')->nullable();
            $table->decimal('tarif', 15, 2);
            $table->string('status');
        });

        DB::table('klasifikasi_biayas')->insert(['kode' => 'KB-PEL', 'nama' => 'Tagihan Pelindo']);
        DB::table('manifests')->insert(['nama_kapal' => 'KM. ALKEN PRINCESS', 'no_voyage' => 'AP16JB26']);
        $barangId = DB::table('pricelist_pelindos')->insertGetId([
            'kegiatan' => 'Bongkar', 'lokasi' => 'Batam', 'status_kontainer' => 'cargo',
            'tarif' => 2000, 'status' => 'aktif',
        ]);
        DB::table('pricelist_pelindos')->insert([
            'kegiatan' => 'Kebersihan', 'lokasi' => 'Batam', 'tarif' => 500, 'status' => 'aktif',
        ]);

        $sections = [[
            'kapal' => 'KM ALKEN PRINCESS', 'voyage' => 'AP16JB26',
            'tagihans' => [[
                'jenis_tagihan' => 'Bongkar', 'lokasi' => 'Batam',
                'barangs' => [['barang' => $barangId, 'jumlah' => 2]],
            ]],
        ]];
        $request = Request::create('/', 'POST', ['pelindo_sections' => $sections]);
        $method = new \ReflectionMethod(BiayaKapalController::class, 'preparePelindoSections');
        [$validated, $details, $isPelindo] = $method->invoke(app(BiayaKapalController::class), $request, [
            'jenis_biaya' => 'KB-PEL', 'pelindo_sections' => $sections, 'pelindo_adjustment' => -100,
        ]);

        $this->assertTrue($isPelindo);
        $this->assertSame('14400.00', $validated['nominal']);
        $this->assertSame(['KM ALKEN PRINCESS'], $validated['nama_kapal']);
        $this->assertSame('KM ALKEN PRINCESS', $details[0]['nama_kapal']);
        $this->assertSame('AP16JB26', $details[0]['no_voyage']);
        $this->assertCount(1, $details);
    }
}
