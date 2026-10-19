<?php

namespace Tests\Feature;

use App\Models\BiayaKapalBuruhBongkar;
use App\Models\PenerimaBuktiPotongPph;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BiayaKapalBuruhBongkarPphRecipientTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
        DB::purge('sqlite');

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        (require database_path('migrations/2026_10_17_000006_create_master_penerima_bukti_potong_pph_table.php'))->up();
        (require database_path('migrations/2026_10_19_000001_add_pph_and_grup_to_master_penerima_bukti_potong_pph.php'))->up();

        Schema::create('biaya_kapal_buruh_bongkars', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('biaya_kapal_id');
            $table->timestamps();
        });

        (require database_path('migrations/2026_10_19_000002_add_pph_recipient_to_biaya_kapal_buruh_bongkars.php'))->up();
    }

    public function test_buruh_bongkar_keeps_selected_recipient_and_npwp_snapshot(): void
    {
        $recipient = PenerimaBuktiPotongPph::create([
            'nama' => 'PT Contoh',
            'nik_npwp' => '0012345678901234',
        ]);

        $detail = BiayaKapalBuruhBongkar::create([
            'biaya_kapal_id' => 1,
            'penerima_bukti_potong_pph_id' => $recipient->id,
            'npwp' => '0012345678901234',
        ]);

        $this->assertSame($recipient->id, $detail->fresh()->penerimaBuktiPotongPph->id);
        $this->assertSame('0012345678901234', $detail->fresh()->npwp);
    }
}
