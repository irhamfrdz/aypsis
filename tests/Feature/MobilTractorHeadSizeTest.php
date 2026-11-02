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
    private Mobil $buntut;

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
        (require database_path('migrations/2026_11_02_130000_add_buntut_id_to_mobils_table.php'))->up();
        $this->buntut = Mobil::withoutEvents(fn () => Mobil::create(['kode_no' => 'BT001', 'jenis' => 'BUNTUT 20 FEET']));

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
            'buntut_id' => $this->buntut->id,
        ])));

        $tractor = Mobil::where('kode_no', 'AT100001')->firstOrFail();
        $this->assertSame('panjang', $tractor->ukuran_tractor_head);
        $this->assertTrue($tractor->buntut->is($this->buntut));
    }

    public function test_size_can_be_changed_and_is_cleared_when_type_changes(): void
    {
        $mobil = Mobil::withoutEvents(fn () => Mobil::create([
            'kode_no' => 'AT100002', 'jenis' => 'TRACTOR HEAD', 'ukuran_tractor_head' => 'panjang',
        ]));
        $controller = new MobilController;

        Mobil::withoutEvents(fn () => $controller->update(Request::create('/master/mobil/'.$mobil->id, 'PUT', [
            'kode_no' => $mobil->kode_no, 'jenis' => 'TRACTOR HEAD', 'ukuran_tractor_head' => 'pendek',
            'buntut_id' => $this->buntut->id,
        ]), $mobil->id));
        $this->assertSame('pendek', $mobil->fresh()->ukuran_tractor_head);

        Mobil::withoutEvents(fn () => $controller->update(Request::create('/master/mobil/'.$mobil->id, 'PUT', [
            'kode_no' => $mobil->kode_no, 'jenis' => 'TRUK', 'ukuran_tractor_head' => 'panjang',
        ]), $mobil->id));
        $this->assertNull($mobil->fresh()->ukuran_tractor_head);
        $this->assertNull($mobil->fresh()->buntut_id);
    }

    public function test_tractor_head_requires_an_existing_buntut_and_rejects_other_vehicle_types(): void
    {
        $other = Mobil::withoutEvents(fn () => Mobil::create(['kode_no' => 'TR001', 'jenis' => 'TRUK']));
        foreach ([null, 999999, $other->id] as $buntutId) {
            try {
                (new MobilController)->store(Request::create('/master/mobil', 'POST', [
                    'kode_no' => 'AT100003', 'jenis' => ' tractor head ', 'ukuran_tractor_head' => 'panjang',
                    'buntut_id' => $buntutId,
                ]));
                $this->fail('Buntut kosong, tidak ada, atau bukan jenis BUNTUT harus ditolak.');
            } catch (ValidationException $exception) {
                $this->assertArrayHasKey('buntut_id', $exception->errors());
            }
        }
    }

    public function test_buntut_can_be_changed_and_non_tractor_head_does_not_save_a_buntut(): void
    {
        $otherBuntut = Mobil::withoutEvents(fn () => Mobil::create(['kode_no' => 'BT002', 'jenis' => ' buntut ']));
        $tractor = Mobil::withoutEvents(fn () => Mobil::create([
            'kode_no' => 'AT100004', 'jenis' => 'TRACTOR HEAD', 'ukuran_tractor_head' => 'panjang',
            'buntut_id' => $this->buntut->id,
        ]));
        $controller = new MobilController;
        foreach ([null, $tractor->id] as $invalid) {
            try {
                $controller->update(Request::create('/master/mobil/'.$tractor->id, 'PUT', [
                    'kode_no' => $tractor->kode_no, 'jenis' => 'TRACTOR HEAD', 'ukuran_tractor_head' => 'panjang',
                    'buntut_id' => $invalid,
                ]), $tractor->id);
                $this->fail('Buntut wajib dan tidak boleh menunjuk kendaraan itu sendiri.');
            } catch (ValidationException $exception) {
                $this->assertArrayHasKey('buntut_id', $exception->errors());
            }
        }
        Mobil::withoutEvents(fn () => $controller->update(Request::create('/master/mobil/'.$tractor->id, 'PUT', [
            'kode_no' => $tractor->kode_no, 'jenis' => 'TRACTOR HEAD', 'ukuran_tractor_head' => 'pendek',
            'buntut_id' => $otherBuntut->id,
        ]), $tractor->id));
        $this->assertTrue($tractor->fresh()->buntut->is($otherBuntut));

        Mobil::withoutEvents(fn () => $controller->store(Request::create('/master/mobil', 'POST', [
            'kode_no' => 'TR002', 'jenis' => 'TRUK', 'buntut_id' => $otherBuntut->id,
        ])));
        $this->assertNull(Mobil::where('kode_no', 'TR002')->firstOrFail()->buntut_id);
    }
}
