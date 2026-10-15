<?php

namespace Tests\Feature;

use App\Http\Controllers\PembayaranPranotaUangJalanController;
use App\Models\PembayaranPranotaUangJalan;
use App\Models\PranotaUangJalan;
use App\Models\SuratJalan;
use App\Services\CoaTransactionService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PembayaranPranotaUangJalanAccurateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');

        Schema::create('pranota_uang_jalans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pranota');
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('pembayaran_pranota_uang_jalans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_accurate')->nullable();
            $table->date('tanggal_pembayaran');
            $table->string('status_pembayaran');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('pembayaran_pranota_uang_jalan_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pembayaran_pranota_uang_jalan_id');
            $table->unsignedBigInteger('pranota_uang_jalan_id');
            $table->decimal('subtotal', 15, 2);
            $table->string('nomor_accurate')->nullable();
            $table->timestamps();
        });

        DB::table('pranota_uang_jalans')->insert([
            ['id' => 1, 'nomor_pranota' => 'PUJ-1026-001137'],
            ['id' => 2, 'nomor_pranota' => 'PUJ-1026-001136'],
        ]);
        DB::table('pembayaran_pranota_uang_jalans')->insert([
            'id' => 1, 'nomor_accurate' => 'BTJ26 1000179',
            'tanggal_pembayaran' => '2026-10-15', 'status_pembayaran' => 'paid',
        ]);
        DB::table('pembayaran_pranota_uang_jalan_items')->insert([
            ['pembayaran_pranota_uang_jalan_id' => 1, 'pranota_uang_jalan_id' => 1, 'subtotal' => 765000],
            ['pembayaran_pranota_uang_jalan_id' => 1, 'pranota_uang_jalan_id' => 2, 'subtotal' => 1335000],
        ]);
    }

    public function test_editing_one_pranota_does_not_change_the_other_pranota_number(): void
    {
        $payment = PembayaranPranotaUangJalan::findOrFail(1);
        $controller = new PembayaranPranotaUangJalanController(app(CoaTransactionService::class));
        $controller->update(Request::create('/', 'PUT', [
            'pranota_id' => 1,
            'nomor_accurate' => 'BTJ26 1000181',
        ]), $payment);

        $this->assertSame('BTJ26 1000179', $payment->fresh()->nomor_accurate);
        $this->assertSame('BTJ26 1000181', PranotaUangJalan::findOrFail(1)->pembayaranPranotaUangJalans->first()->nomor_accurate);
        $this->assertSame('BTJ26 1000179', PranotaUangJalan::findOrFail(2)->pembayaranPranotaUangJalans->first()->nomor_accurate);
    }

    public function test_joined_surat_jalan_payment_uses_its_pranota_number(): void
    {
        Schema::create('uang_jalans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('surat_jalan_id');
        });
        Schema::create('pranota_uang_jalan_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('uang_jalan_id');
            $table->unsignedBigInteger('pranota_uang_jalan_id');
        });
        DB::table('uang_jalans')->insert(['id' => 1, 'surat_jalan_id' => 1]);
        DB::table('pranota_uang_jalan_items')->insert([
            'uang_jalan_id' => 1, 'pranota_uang_jalan_id' => 1,
        ]);
        DB::table('pembayaran_pranota_uang_jalan_items')->where('pranota_uang_jalan_id', 1)
            ->update(['nomor_accurate' => 'BTJ26 1000181']);

        $suratJalan = new SuratJalan;
        $suratJalan->id = 1;
        $this->assertSame('BTJ26 1000181', $suratJalan->pembayaranPranotaUangJalan->nomor_accurate);
    }

    public function test_edit_rejects_a_pranota_outside_the_payment(): void
    {
        DB::table('pranota_uang_jalans')->insert(['id' => 3, 'nomor_pranota' => 'PUJ-1026-001138']);
        $payment = PembayaranPranotaUangJalan::findOrFail(1);
        $controller = new PembayaranPranotaUangJalanController(app(CoaTransactionService::class));

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        try {
            $controller->update(Request::create('/', 'PUT', [
                'pranota_id' => 3,
                'nomor_accurate' => 'BTJ26 1000181',
            ]), $payment);
        } finally {
            $this->assertSame('BTJ26 1000179', $payment->fresh()->nomor_accurate);
            $this->assertNull(DB::table('pembayaran_pranota_uang_jalan_items')->where('pranota_uang_jalan_id', 1)->value('nomor_accurate'));
        }
    }
}
