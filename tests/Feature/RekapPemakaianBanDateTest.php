<?php

namespace Tests\Feature;

use App\Http\Controllers\RekapPemakaianBanController;
use Illuminate\Http\Request;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class RekapPemakaianBanDateTest extends TestCase
{
    public function test_ring_velg_counts_only_matching_usage_in_selected_period_and_includes_ring_only_units(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('mobils', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_polisi');
            $table->string('no_kir')->nullable();
        });
        Schema::create('stock_ban_usage_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mobil_id')->nullable();
            $table->string('unit_name')->nullable();
            $table->date('tanggal_digunakan')->nullable();
            $table->date('tanggal_keluar')->nullable();
            $table->timestamps();
        });
        Schema::create('stock_ban_dalam_usages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mobil_id')->nullable();
            $table->unsignedBigInteger('stock_ban_dalam_id')->nullable();
            $table->unsignedBigInteger('penerima_id')->nullable();
            $table->date('tanggal_keluar')->nullable();
            $table->text('keterangan')->nullable();
            $table->integer('qty');
        });
        DB::table('mobils')->insert([['id' => 1, 'nomor_polisi' => 'A'], ['id' => 2, 'nomor_polisi' => 'B']]);
        DB::table('stock_ban_usage_histories')->insert(['mobil_id' => 1, 'tanggal_digunakan' => '2026-10-10']);
        DB::table('stock_ban_dalam_usages')->insert([
            ['mobil_id' => 1, 'stock_ban_dalam_id' => null, 'tanggal_keluar' => '2026-10-01', 'keterangan' => '[Ring Velg ID: 1] dipakai', 'qty' => 3],
            ['mobil_id' => 2, 'stock_ban_dalam_id' => null, 'tanggal_keluar' => '2026-10-31', 'keterangan' => '[Ring Velg ID: 2] dipakai', 'qty' => 1],
            ['mobil_id' => 1, 'stock_ban_dalam_id' => null, 'tanggal_keluar' => '2026-10-02', 'keterangan' => '[Velg ID: 1] dipakai', 'qty' => 1],
            ['mobil_id' => 1, 'stock_ban_dalam_id' => 1, 'tanggal_keluar' => '2026-10-02', 'keterangan' => '[Ring Velg ID: 1] lainnya', 'qty' => 1],
            ['mobil_id' => 1, 'stock_ban_dalam_id' => null, 'tanggal_keluar' => '2026-11-01', 'keterangan' => '[Ring Velg ID: 1] dipakai', 'qty' => 1],
        ]);

        $data = (new RekapPemakaianBanController)->index(Request::create('/', 'GET', [
            'start_date' => '2026-10-01', 'end_date' => '2026-10-31',
        ]))->getData();
        $units = $data['units']->keyBy('mobil_id');
        $this->assertCount(2, $units);
        $this->assertEquals(1, $units[1]->total);
        $this->assertEquals(1, $units[1]->ring_total);
        $this->assertEquals(0, $units[2]->total);
        $this->assertEquals(1, $units[2]->ring_total);

        $detail = (new RekapPemakaianBanController)->index(Request::create('/', 'GET', [
            'start_date' => '2026-10-01', 'end_date' => '2026-10-31', 'unit' => 2,
        ]))->getData();
        $this->assertSame(1, $detail['ringItems']->total());
        $this->assertSame('[Ring Velg ID: 2] dipakai', $detail['ringItems']->first()->keterangan);
    }

    public function test_initial_view_waits_for_date_and_unit_selection(): void
    {
        $data = (new RekapPemakaianBanController)->index(Request::create('/', 'GET'))->getData();
        $this->assertNull($data['items']);
        $this->assertTrue($data['units']->isEmpty());
    }

    public function test_invalid_range_or_unit_without_dates_is_rejected(): void
    {
        foreach ([['unit' => 1], ['start_date' => '2026-11-10', 'end_date' => '2026-11-01']] as $filters) {
            try {
                (new RekapPemakaianBanController)->index(Request::create('/', 'GET', $filters));
                $this->fail('Expected validation failure.');
            } catch (ValidationException $exception) {
                $this->assertNotEmpty($exception->errors());
            }
        }
    }
}
