<?php

namespace Tests\Feature;

use App\Http\Controllers\PranotaLemburController;
use App\Models\PranotaLembur;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mockery;
use Tests\TestCase;

class PranotaLemburRemoveSuratJalanTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('pranota_lemburs', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pranota');
            $table->string('status');
            $table->decimal('total_biaya', 15, 2);
            $table->decimal('adjustment', 15, 2);
            $table->decimal('total_setelah_adjustment', 15, 2);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pranota_lembur_surat_jalan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pranota_lembur_id');
            $table->unsignedBigInteger('surat_jalan_id')->nullable();
            $table->unsignedBigInteger('surat_jalan_bongkaran_id')->nullable();
            $table->decimal('total_biaya', 15, 2);
        });

        $user = Mockery::mock(User::class)->makePartial();
        $user->id = 7;
        $user->shouldReceive('can')->with('pranota-lembur-update')->andReturn(true);
        Auth::shouldReceive('user')->andReturn($user);
    }

    public function test_removing_one_item_recalculates_totals_without_touching_other_pranotas(): void
    {
        DB::table('pranota_lemburs')->insert([
            ['id' => 1, 'nomor_pranota' => 'PML-1', 'status' => 'draft', 'total_biaya' => 300, 'adjustment' => 25, 'total_setelah_adjustment' => 325],
            ['id' => 2, 'nomor_pranota' => 'PML-2', 'status' => 'draft', 'total_biaya' => 100, 'adjustment' => 0, 'total_setelah_adjustment' => 100],
        ]);
        DB::table('pranota_lembur_surat_jalan')->insert([
            ['pranota_lembur_id' => 1, 'surat_jalan_id' => 10, 'surat_jalan_bongkaran_id' => null, 'total_biaya' => 100],
            ['pranota_lembur_id' => 1, 'surat_jalan_id' => null, 'surat_jalan_bongkaran_id' => 20, 'total_biaya' => 200],
            ['pranota_lembur_id' => 2, 'surat_jalan_id' => 10, 'surat_jalan_bongkaran_id' => null, 'total_biaya' => 100],
        ]);

        PranotaLembur::withoutEvents(function () {
            app(PranotaLemburController::class)->removeSuratJalan(PranotaLembur::findOrFail(1), 'muat', 10);
        });

        $this->assertDatabaseMissing('pranota_lembur_surat_jalan', ['pranota_lembur_id' => 1, 'surat_jalan_id' => 10]);
        $this->assertDatabaseHas('pranota_lembur_surat_jalan', ['pranota_lembur_id' => 1, 'surat_jalan_bongkaran_id' => 20]);
        $this->assertDatabaseHas('pranota_lembur_surat_jalan', ['pranota_lembur_id' => 2, 'surat_jalan_id' => 10]);
        $this->assertEquals(200, PranotaLembur::findOrFail(1)->total_biaya);
        $this->assertEquals(225, PranotaLembur::findOrFail(1)->total_setelah_adjustment);

        PranotaLembur::withoutEvents(function () {
            app(PranotaLemburController::class)->removeSuratJalan(PranotaLembur::findOrFail(1), 'bongkaran', 20);
        });

        $this->assertDatabaseMissing('pranota_lembur_surat_jalan', ['pranota_lembur_id' => 1]);
        $this->assertEquals(0, PranotaLembur::findOrFail(1)->total_biaya);
        $this->assertEquals(25, PranotaLembur::findOrFail(1)->total_setelah_adjustment);

        app(PranotaLemburController::class)->submit(PranotaLembur::findOrFail(1));
        $this->assertSame('draft', PranotaLembur::findOrFail(1)->status);
    }

    public function test_submitted_pranota_cannot_be_changed(): void
    {
        DB::table('pranota_lemburs')->insert([
            'id' => 1, 'nomor_pranota' => 'PML-1', 'status' => 'submitted',
            'total_biaya' => 100, 'adjustment' => 0, 'total_setelah_adjustment' => 100,
        ]);
        DB::table('pranota_lembur_surat_jalan')->insert([
            'pranota_lembur_id' => 1, 'surat_jalan_id' => 10, 'total_biaya' => 100,
        ]);

        app(PranotaLemburController::class)->removeSuratJalan(PranotaLembur::findOrFail(1), 'muat', 10);

        $this->assertDatabaseHas('pranota_lembur_surat_jalan', ['pranota_lembur_id' => 1, 'surat_jalan_id' => 10]);
        $this->assertEquals(100, PranotaLembur::findOrFail(1)->total_biaya);
    }
}
