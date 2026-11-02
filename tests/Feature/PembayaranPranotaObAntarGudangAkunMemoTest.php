<?php

namespace Tests\Feature;

use App\Http\Controllers\PembayaranPranotaObAntarGudangController;
use App\Models\CoaTransaction;
use App\Models\PembayaranPranotaObAntarGudang;
use App\Models\User;
use App\Services\CoaTransactionService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PembayaranPranotaObAntarGudangAkunMemoTest extends TestCase
{
    public function test_each_account_memo_is_saved_and_recorded_in_its_own_journal_line(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });
        Schema::create('akun_coa', function (Blueprint $table) {
            $table->id();
            $table->string('nama_akun');
        });
        Schema::create('pranota_ob_antar_gudangs', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pranota');
            $table->string('status_pembayaran');
            $table->decimal('grand_total', 15, 2);
            $table->timestamps();
        });
        (require database_path('migrations/2026_06_17_134000_create_pembayaran_pranota_ob_antar_gudangs_table.php'))->up();
        (require database_path('migrations/2026_11_02_000002_create_pembayaran_pranota_ob_antar_gudang_akun_items_table.php'))->up();

        DB::table('users')->insert(['id' => 1, 'name' => 'Penguji']);
        DB::table('akun_coa')->insert([
            ['id' => 1, 'nama_akun' => 'Biaya OB'],
            ['id' => 2, 'nama_akun' => 'Biaya Tambahan'],
            ['id' => 3, 'nama_akun' => 'Bank'],
        ]);
        DB::table('pranota_ob_antar_gudangs')->insert([
            'id' => 1, 'nomor_pranota' => 'AG-001', 'status_pembayaran' => 'Belum Lunas', 'grand_total' => 2380000,
        ]);
        $user = new User;
        $user->setRawAttributes(['id' => 1, 'name' => 'Penguji']);
        $this->actingAs($user);

        $journal = [];
        $service = $this->mock(CoaTransactionService::class);
        $service->shouldReceive('recordTransaction')->times(6)
            ->andReturnUsing(function ($nama, $debit, $kredit, $tanggal, $referensi, $jenis, $keterangan, $coaId) use (&$journal) {
                $journal[] = ['coa_id' => $coaId, 'debit' => $debit, 'kredit' => $kredit, 'keterangan' => $keterangan];
                return new CoaTransaction;
            });
        $service->shouldReceive('deleteTransactionByReference')->once()->andReturn(true);
        $controller = new PembayaranPranotaObAntarGudangController($service);
        $data = [
            'nomor_pembayaran' => 'PY-AG-TEST', 'debit_kredit' => 'credit', 'akun_bank_id' => 3,
            'tanggal_kas' => '2026-11-02', 'pranota_ids' => [1], 'penyesuaian' => 0,
            'akun_items' => [
                ['akun_coa_id' => 1, 'nominal' => 2000000, 'memo' => 'Biaya kirim kontainer'],
                ['akun_coa_id' => 2, 'nominal' => 380000, 'memo' => 'Biaya bongkar gudang'],
            ],
        ];
        $controller->store(Request::create('/pembayaran-pranota-ob-antar-gudang', 'POST', $data));

        $payment = PembayaranPranotaObAntarGudang::firstOrFail();
        $this->assertSame(['Biaya kirim kontainer', 'Biaya bongkar gudang'], $payment->akunItems->pluck('memo')->all());
        $this->assertSame([2000000, 380000], $payment->akunItems->pluck('nominal')->map(fn ($value) => (int) $value)->all());
        $this->assertEquals([1, 2, 3], array_column($journal, 'coa_id'));
        $this->assertEquals(2380000, array_sum(array_column($journal, 'debit')));
        $this->assertEquals(2380000, array_sum(array_column($journal, 'kredit')));
        $this->assertStringContainsString('Biaya kirim kontainer', $journal[0]['keterangan']);
        $this->assertStringContainsString('Biaya bongkar gudang', $journal[1]['keterangan']);
        $this->assertStringNotContainsString('Memo akun:', $journal[2]['keterangan']);

        $data['akun_items'][1]['memo'] = 'Biaya bongkar revisi';
        unset($data['pranota_ids'], $data['nomor_pembayaran']);
        $controller->update(Request::create('/pembayaran-pranota-ob-antar-gudang/'.$payment->id, 'PUT', $data), $payment->id);

        $this->assertSame('Biaya bongkar revisi', $payment->fresh()->akunItems->last()->memo);
        $this->assertStringContainsString('Biaya bongkar revisi', $journal[4]['keterangan']);
    }
}
