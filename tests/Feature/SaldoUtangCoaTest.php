<?php

namespace Tests\Feature;

use App\Http\Controllers\SaldoUtangSupirController;
use App\Models\RiwayatUtangKaryawan;
use App\Models\SaldoUtangKaryawan;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SaldoUtangCoaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap');
            $table->string('nik')->nullable();
            $table->softDeletes();
        });
        Schema::create('akun_coa', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_akun');
            $table->string('nama_akun');
            $table->string('tipe_akun');
        });
        (require database_path('migrations/2026_06_27_090052_create_saldo_utang_supirs_tables.php'))->up();
        Schema::rename('saldo_utang_supirs', 'saldo_utang_karyawans');
        Schema::rename('riwayat_utang_supirs', 'riwayat_utang_karyawans');
        (require database_path('migrations/2026_11_14_100000_add_coa_details_to_riwayat_utang_karyawans.php'))->up();
        DB::table('karyawans')->insert(['id' => 1, 'nama_lengkap' => 'Karyawan Uji', 'nik' => '001']);
        DB::table('akun_coa')->insert([
            ['id' => 1, 'nomor_akun' => '1001', 'nama_akun' => 'Bank Uji', 'tipe_akun' => 'Kas/Bank'],
            ['id' => 2, 'nomor_akun' => '1101', 'nama_akun' => 'Piutang Karyawan', 'tipe_akun' => 'Aset'],
            ['id' => 3, 'nomor_akun' => '1102', 'nama_akun' => 'Piutang Lain', 'tipe_akun' => 'Aset'],
        ]);
    }

    private function payload(): array
    {
        return ['karyawan_id' => 1, 'tanggal' => '2026-11-14', 'tipe' => 'penambahan',
            'nominal' => 999, 'referensi' => 'Kasbon', 'bank_coa_id' => 1,
            'rincian_coa' => [2 => ['coa_id' => 2, 'nominal' => 100000.25, 'memo' => 'Memo pertama'], 5 => ['coa_id' => 3, 'nominal' => 49999.75, 'memo' => 'Memo kedua']]];
    }

    public function test_multiple_accounts_and_memos_are_saved_with_the_balance_and_shown_in_history(): void
    {
        $controller = new SaldoUtangSupirController;
        $controller->store(Request::create('/', 'POST', $this->payload()));
        $this->assertFalse(session()->has('error'), (string) session('error'));
        $entry = RiwayatUtangKaryawan::firstOrFail();
        $this->assertEquals(1, $entry->bank_coa_id);
        $this->assertSame('Bank Uji', $entry->bankCoa->nama_akun);
        $this->assertSame(['Memo pertama', 'Memo kedua'], array_column($entry->rincian_coa, 'memo'));
        $this->assertSame([2, 3], array_column($entry->rincian_coa, 'coa_id'));
        $this->assertSame([100000.25, 49999.75], array_column($entry->rincian_coa, 'nominal'));
        $this->assertSame('150000.00', $entry->nominal);
        $this->assertEquals(150000, SaldoUtangKaryawan::firstOrFail()->saldo);
        $source = str_replace("@extends('layouts.app')", '', file_get_contents(resource_path('views/saldo-utang-supir/show.blade.php')));
        $html = Blade::render($source."\n@yield('content')", $controller->show(1)->getData());
        foreach (['Bank Uji', 'Piutang Karyawan', 'Memo pertama', 'Memo kedua'] as $text) {
            $this->assertStringContainsString($text, $html);
        }
        $payload = $this->payload();
        $payload['tipe'] = 'pengurangan';
        unset($payload['nominal']);
        $payload['rincian_coa'][2]['nominal'] = 20000.25;
        $payload['rincian_coa'][5]['nominal'] = 29999.75;
        $controller->store(Request::create('/', 'POST', $payload));
        $this->assertEquals(100000, SaldoUtangKaryawan::firstOrFail()->saldo);
    }

    public function test_invalid_bank_or_account_and_missing_rows_do_not_change_balance(): void
    {
        foreach ([['bank_coa_id' => 2], ['rincian_coa' => []], ['rincian_coa' => [['coa_id' => 999, 'nominal' => 1]]], ['rincian_coa' => [['coa_id' => 2, 'nominal' => 1, 'memo' => str_repeat('x', 1001)]]],
            ['rincian_coa' => [['coa_id' => 2]]], ['rincian_coa' => [['coa_id' => 2, 'nominal' => 0]]],
            ['rincian_coa' => [['coa_id' => 2, 'nominal' => -1]]], ['rincian_coa' => [['coa_id' => 2, 'nominal' => '1.234']]],
            ['rincian_coa' => [['coa_id' => 2, 'nominal' => 9999999999999], ['coa_id' => 3, 'nominal' => 2]]]] as $invalid) {
            try {
                (new SaldoUtangSupirController)->store(Request::create('/', 'POST', array_replace($this->payload(), $invalid)));
                $this->fail('Invalid account data must be rejected.');
            } catch (ValidationException $exception) {
                $this->assertNotEmpty($exception->errors());
            }
        }
        $this->assertSame(0, SaldoUtangKaryawan::count());
        $this->assertSame(0, RiwayatUtangKaryawan::count());
    }

    public function test_create_form_lists_bank_accounts_and_renders_dynamic_coa_rows(): void
    {
        $data = (new SaldoUtangSupirController)->create()->getData();
        $data['errors'] = new \Illuminate\Support\ViewErrorBag;
        $this->assertSame([1], $data['bankAccounts']->pluck('id')->values()->all());
        $source = str_replace("@extends('layouts.app')", '', file_get_contents(resource_path('views/saldo-utang-supir/create.blade.php')));
        $html = Blade::render($source."\n@yield('content')", $data);
        foreach (['bank_coa_id', 'rincian_coa[0][coa_id]', 'rincian_coa[0][nominal]', 'rincian_coa[0][memo]', 'Grand Total (Rupiah)', 'coa-row-template', 'Tambah Akun', 'Piutang Karyawan'] as $text) {
            $this->assertStringContainsString($text, $html);
        }
    }
}
