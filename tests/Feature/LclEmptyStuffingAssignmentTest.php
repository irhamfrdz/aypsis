<?php

namespace Tests\Feature;

use App\Http\Controllers\TandaTerimaLclController;
use App\Models\TandaTerimaLcl;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LclEmptyStuffingAssignmentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('tanda_terimas_lcl', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_tanda_terima');
            $table->string('nama_penerima')->nullable();
            $table->string('nama_pengirim')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('tanda_terima_lcl_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tanda_terima_lcl_id');
        });
        Schema::create('tanda_terima_lcl_kontainer_pivot', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tanda_terima_lcl_id');
            $table->string('nomor_kontainer')->nullable();
            $table->string('size_kontainer')->nullable();
            $table->string('tipe_kontainer')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->timestamps();
        });
        foreach (range(1, 5) as $id) {
            DB::table('tanda_terimas_lcl')->insert(['id' => $id, 'nomor_tanda_terima' => 'LS1-TEST'.$id]);
        }
        foreach ([1 => null, 2 => '', 3 => '   ', 4 => 'EXISTING'] as $id => $number) {
            DB::table('tanda_terima_lcl_kontainer_pivot')->insert(['id' => $id, 'tanda_terima_lcl_id' => $id, 'nomor_kontainer' => $number]);
        }
    }

    public function test_empty_assignments_are_available_but_assigned_receipts_are_hidden(): void
    {
        $this->assertEquals([1, 2, 3, 5], TandaTerimaLcl::unstuffed()->orderBy('id')->pluck('id')->all());
        $response = (new TandaTerimaLclController)->unstuffedForStuffing(Request::create('/', 'GET', ['search' => 'LS1-TEST1']));
        $this->assertStringContainsString('LS1-TEST1', $response->getData(true)['rows']);
    }

    public function test_stuffing_reuses_empty_pivots_and_preserves_existing_containers(): void
    {
        $request = Request::create('/', 'POST', [
            'tanda_terima_ids' => [1, 2, 3, 4, 5], 'nomor_kontainer' => 'NEW123',
            'size_kontainer' => '20ft', 'tipe_kontainer' => 'LCL',
        ]);
        $response = (new TandaTerimaLclController)->processStuffing($request);
        $this->assertSame(302, $response->getStatusCode());
        foreach ([1, 2, 3] as $id) {
            $this->assertDatabaseHas('tanda_terima_lcl_kontainer_pivot', ['id' => $id, 'tanda_terima_lcl_id' => $id, 'nomor_kontainer' => 'NEW123']);
        }
        $this->assertDatabaseHas('tanda_terima_lcl_kontainer_pivot', ['tanda_terima_lcl_id' => 5, 'nomor_kontainer' => 'NEW123']);
        $this->assertDatabaseHas('tanda_terima_lcl_kontainer_pivot', ['id' => 4, 'nomor_kontainer' => 'EXISTING']);
        $this->assertDatabaseCount('tanda_terima_lcl_kontainer_pivot', 5);
        $this->assertSame(0, TandaTerimaLcl::unstuffed()->count());
        (new TandaTerimaLclController)->processStuffing($request);
        $this->assertDatabaseCount('tanda_terima_lcl_kontainer_pivot', 5);
    }

    public function test_a_receipt_with_both_empty_and_assigned_pivots_cannot_be_stuffed_again(): void
    {
        DB::table('tanda_terima_lcl_kontainer_pivot')->insert(['tanda_terima_lcl_id' => 1, 'nomor_kontainer' => 'EXISTING']);
        $this->assertFalse(TandaTerimaLcl::unstuffed()->whereKey(1)->exists());
        (new TandaTerimaLclController)->processStuffing(Request::create('/', 'POST', ['tanda_terima_ids' => [1], 'nomor_kontainer' => 'NEW123']));
        $this->assertDatabaseMissing('tanda_terima_lcl_kontainer_pivot', ['nomor_kontainer' => 'NEW123']);
    }
}
