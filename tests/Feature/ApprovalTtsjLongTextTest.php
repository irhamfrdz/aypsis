<?php

namespace Tests\Feature;

use App\Models\TandaTerimaTanpaSuratJalan;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ApprovalTtsjLongTextTest extends TestCase
{
    public function test_receipt_text_can_exceed_255_characters_and_number_remains_unique(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');

        Schema::create('tanda_terima_tanpa_surat_jalan', function (Blueprint $table) {
            $table->id();
            $table->string('no_tanda_terima')->nullable()->unique();
            $table->index('no_tanda_terima');
            $table->string('nomor_tanda_terima')->nullable();
            $table->string('pengirim')->nullable();
            $table->index('pengirim');
            $table->string('penerima')->nullable();
            $table->index('penerima');
            $table->string('notify_party')->nullable();
            $table->text('alamat_pengirim')->nullable();
            $table->text('alamat_penerima')->nullable();
            $table->text('alamat_notify_party')->nullable();
            $table->string('tujuan_pengiriman')->nullable();
            $table->string('no_kontainer')->nullable();
            $table->string('size_kontainer')->nullable();
            $table->string('no_seal')->nullable();
            $table->string('nama_barang')->nullable();
            $table->text('jenis_barang')->nullable();
            $table->string('satuan_barang')->nullable();
            $table->text('keterangan_barang')->nullable();
            $table->timestamps();
        });
        Schema::create('manifests', function (Blueprint $table) {
            $table->id();
            foreach (['nomor_tanda_terima', 'nama_barang', 'hs_code', 'satuan', 'pengirim', 'penerima', 'notify_party'] as $column) {
                $table->string($column)->nullable();
            }
            foreach (['alamat_pengirim', 'alamat_penerima', 'alamat_notify_party'] as $column) {
                $table->text($column)->nullable();
            }
        });

        DB::table('tanda_terima_tanpa_surat_jalan')->insert(['no_tanda_terima' => 'TT-OLD-001']);

        $migration = require database_path('migrations/2026_11_09_120000_expand_approval_ttsj_text_fields.php');
        $migration->up();

        $this->assertSame(
            hash('sha256', 'tt-old-001'),
            DB::table('tanda_terima_tanpa_surat_jalan')->where('no_tanda_terima', 'TT-OLD-001')->value('no_tanda_terima_digest')
        );

        $longText = str_repeat('A', 300);
        $receipt = TandaTerimaTanpaSuratJalan::create([
            'no_tanda_terima' => $longText,
            'nomor_tanda_terima' => $longText,
            'tujuan_pengiriman' => $longText,
            'nama_barang' => $longText,
            'jenis_barang' => $longText,
        ]);

        $this->assertSame($longText, $receipt->fresh()->nama_barang);
        $this->assertSame(hash('sha256', mb_strtolower($longText)), $receipt->fresh()->no_tanda_terima_digest);

        $this->expectException(\Illuminate\Database\UniqueConstraintViolationException::class);
        TandaTerimaTanpaSuratJalan::create(['no_tanda_terima' => strtolower($longText)]);
    }
}
