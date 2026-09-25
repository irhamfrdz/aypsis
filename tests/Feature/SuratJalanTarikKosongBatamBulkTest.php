<?php

namespace Tests\Feature;

use App\Http\Controllers\SuratJalanTarikKosongBatamController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SuratJalanTarikKosongBatamBulkTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->string('divisi')->nullable();
            $table->string('nama_panggilan')->nullable();
            $table->string('nama_lengkap')->nullable();
        });
        Schema::create('mobils', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_polisi')->nullable();
        });
        Schema::create('gudangs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_gudang');
        });
        Schema::create('stock_kontainers', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_seri_gabungan');
            $table->string('status')->nullable();
        });
        Schema::create('surat_jalan_tarik_kosong_batams', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat_jalan')->unique();
            $table->date('tanggal_surat_jalan')->nullable();
            $table->string('tujuan_pengambilan')->nullable();
            $table->string('supir')->nullable();
            $table->string('no_plat')->nullable();
            $table->string('no_kontainer')->nullable();
            $table->string('size')->nullable();
            $table->string('f_e')->nullable();
            $table->string('status')->nullable();
            $table->text('catatan')->nullable();
            $table->decimal('uang_jalan')->nullable();
            $table->unsignedBigInteger('input_by')->nullable();
            $table->datetime('input_date')->nullable();
            $table->string('lokasi')->nullable();
            $table->timestamps();
        });
    }

    public function test_new_bulk_row_saves_container_from_third_column(): void
    {
        DB::table('gudangs')->insert(['id' => 1, 'nama_gudang' => 'SRIMAS']);

        $response = (new SuratJalanTarikKosongBatamController)->storeBulk(Request::create('/', 'POST', [
            'rows' => [[
                'no_surat_jalan' => '0038591',
                'tanggal_surat_jalan' => '2026-09-25',
                'no_kontainer' => 'FORU8134330',
                'size' => '20',
                'f_e' => 'Empty',
                'supir' => 'RIDWAN',
                'no_plat' => 'BP9302DU',
                'tujuan_pengambilan' => 'BUKIT SENYUM',
                'gudang_tujuan' => 'SRIMAS',
            ]],
        ]));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(1, $response->getData(true)['createdCount']);
        $this->assertDatabaseHas('surat_jalan_tarik_kosong_batams', [
            'no_surat_jalan' => '0038591', 'no_kontainer' => 'FORU8134330',
        ]);
    }

    public function test_reimport_fills_empty_container_without_replacing_existing_one(): void
    {
        DB::table('surat_jalan_tarik_kosong_batams')->insert([
            ['no_surat_jalan' => '0038591', 'no_kontainer' => null],
            ['no_surat_jalan' => '0035602', 'no_kontainer' => 'AYPU9999999'],
        ]);

        $response = (new SuratJalanTarikKosongBatamController)->storeBulk(Request::create('/', 'POST', [
            'rows' => [
                ['no_surat_jalan' => '0038591', 'no_kontainer' => 'FORU8134330'],
                ['no_surat_jalan' => '0035602', 'no_kontainer' => 'AYPU2524182'],
            ],
        ]));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(1, $response->getData(true)['updatedCount']);
        $this->assertSame(1, count($response->getData(true)['errors']));
        $this->assertDatabaseHas('surat_jalan_tarik_kosong_batams', [
            'no_surat_jalan' => '0038591', 'no_kontainer' => 'FORU8134330',
        ]);
        $this->assertDatabaseHas('surat_jalan_tarik_kosong_batams', [
            'no_surat_jalan' => '0035602', 'no_kontainer' => 'AYPU9999999',
        ]);
    }

    public function test_reimport_rejects_empty_container(): void
    {
        DB::table('surat_jalan_tarik_kosong_batams')->insert([
            'no_surat_jalan' => '0038591', 'no_kontainer' => null,
        ]);

        $response = (new SuratJalanTarikKosongBatamController)->storeBulk(Request::create('/', 'POST', [
            'rows' => [['no_surat_jalan' => '0038591', 'no_kontainer' => '']],
        ]));

        $this->assertSame(0, $response->getData(true)['updatedCount']);
        $this->assertNotEmpty($response->getData(true)['errors']);
        $this->assertDatabaseHas('surat_jalan_tarik_kosong_batams', [
            'no_surat_jalan' => '0038591', 'no_kontainer' => null,
        ]);
    }
}
