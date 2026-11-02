<?php

namespace Tests\Feature;

use App\Http\Controllers\MobilController;
use App\Models\Mobil;
use App\Services\MobilAssetSyncService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class MobilTractorHeadSizeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'cache.default' => 'array']);
        DB::purge('sqlite');

        Schema::create('mobils', function (Blueprint $table) {
            $table->id();
            $table->string('kode_no')->unique();
            $table->string('nomor_polisi')->nullable()->unique();
            $table->string('jenis')->nullable();
            $table->boolean('is_asset_karyawan')->default(false);
            $table->timestamps();
        });
        (require database_path('migrations/2026_11_02_100000_add_ukuran_tractor_head_to_mobils_table.php'))->up();

        $this->mock(MobilAssetSyncService::class)
            ->shouldReceive('sync')->andReturn(null);
    }

    public function test_tractor_head_requires_a_valid_size_and_saves_it(): void
    {
        $controller = new MobilController;

        try {
            $controller->store(Request::create('/master/mobil', 'POST', [
                'kode_no' => 'AT100001', 'jenis' => 'TRACTOR HEAD',
            ]));
            $this->fail('Ukuran Tractor Head seharusnya wajib diisi.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('ukuran_tractor_head', $exception->errors());
        }

        try {
            $controller->store(Request::create('/master/mobil', 'POST', [
                'kode_no' => 'AT100001', 'jenis' => 'TRACTOR HEAD', 'ukuran_tractor_head' => 'besar',
            ]));
            $this->fail('Ukuran di luar panjang dan pendek seharusnya ditolak.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('ukuran_tractor_head', $exception->errors());
        }

        Mobil::withoutEvents(fn () => $controller->store(Request::create('/master/mobil', 'POST', [
            'kode_no' => 'AT100001', 'jenis' => 'TRACTOR HEAD', 'ukuran_tractor_head' => 'panjang',
        ])));

        $this->assertSame('panjang', Mobil::firstOrFail()->ukuran_tractor_head);
    }

    public function test_size_can_be_changed_and_is_cleared_when_type_changes(): void
    {
        $mobil = Mobil::withoutEvents(fn () => Mobil::create([
            'kode_no' => 'AT100002', 'jenis' => 'TRACTOR HEAD', 'ukuran_tractor_head' => 'panjang',
        ]));
        $controller = new MobilController;

        Mobil::withoutEvents(fn () => $controller->update(Request::create('/master/mobil/'.$mobil->id, 'PUT', [
            'kode_no' => $mobil->kode_no, 'jenis' => 'TRACTOR HEAD', 'ukuran_tractor_head' => 'pendek',
        ]), $mobil->id));
        $this->assertSame('pendek', $mobil->fresh()->ukuran_tractor_head);

        Mobil::withoutEvents(fn () => $controller->update(Request::create('/master/mobil/'.$mobil->id, 'PUT', [
            'kode_no' => $mobil->kode_no, 'jenis' => 'TRUK', 'ukuran_tractor_head' => 'panjang',
        ]), $mobil->id));
        $this->assertNull($mobil->fresh()->ukuran_tractor_head);
    }
}
