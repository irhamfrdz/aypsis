<?php

namespace Tests\Feature;

use App\Http\Controllers\InvoiceKasPinangController;
use App\Models\Coa;
use App\Models\CoaTransaction;
use App\Models\InvoiceKasPinang;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class InvoiceKasPinangTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', fn (Blueprint $table) => $table->id());
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });
        Schema::create('akun_coa', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_akun')->unique();
            $table->string('nama_akun');
            $table->string('tipe_akun');
            $table->decimal('saldo', 15, 2)->default(0);
            $table->timestamps();
        });
        Schema::create('coa_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coa_id');
            $table->date('tanggal_transaksi');
            $table->string('nomor_referensi')->nullable();
            $table->string('jenis_transaksi');
            $table->text('keterangan')->nullable();
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('kredit', 15, 2)->default(0);
            $table->decimal('saldo', 15, 2)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->string('auditable_type');
            $table->unsignedBigInteger('auditable_id');
            $table->string('action');
            $table->string('module')->nullable();
            $table->string('description')->nullable();
            $table->longText('old_values')->nullable();
            $table->longText('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('url')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        (require database_path('migrations/2026_11_05_000001_create_invoice_kas_pinang_tables.php'))->up();
    }

    public function test_draft_does_not_post_and_issued_cash_flow_is_reversed_on_update_and_delete(): void
    {
        $kas = Coa::create(['nomor_akun' => '1001', 'nama_akun' => 'Kas Pinang', 'tipe_akun' => 'Aset', 'saldo' => 0]);
        $biaya = Coa::create(['nomor_akun' => '5001', 'nama_akun' => 'Biaya Operasional', 'tipe_akun' => 'Beban', 'saldo' => 0]);
        $controller = app(InvoiceKasPinangController::class);

        $data = [
            'tanggal_transaksi' => '2026-11-05',
            'arus_kas' => 'keluar',
            'akun_kas_id' => $kas->id,
            'akun_lawan_id' => $biaya->id,
            'status' => 'draft',
            'items' => [['uraian' => 'Biaya pelabuhan', 'jumlah' => '2', 'harga_satuan' => '100.00']],
        ];
        $controller->store(Request::create('/', 'POST', $data));
        $invoice = InvoiceKasPinang::firstOrFail();
        $this->assertSame('200.00', $invoice->total);
        $this->assertCount(1, $invoice->items);
        $this->assertSame(0, CoaTransaction::count());

        $controller->update(Request::create('/', 'PUT', [...$data, 'status' => 'terbit']), $invoice);
        $this->assertSame('-200.00', $kas->fresh()->saldo);
        $this->assertSame('200.00', $biaya->fresh()->saldo);
        $this->assertSame(2, CoaTransaction::where('nomor_referensi', $invoice->nomor_invoice)->count());

        $controller->update(Request::create('/', 'PUT', [...$data, 'status' => 'terbit', 'arus_kas' => 'masuk']), $invoice);
        $this->assertSame('200.00', $kas->fresh()->saldo);
        $this->assertSame('-200.00', $biaya->fresh()->saldo);
        $this->assertSame(2, CoaTransaction::where('nomor_referensi', $invoice->nomor_invoice)->count());

        $controller->update(Request::create('/', 'PUT', [...$data, 'status' => 'draft']), $invoice);
        $this->assertSame('0.00', $kas->fresh()->saldo);
        $this->assertSame('0.00', $biaya->fresh()->saldo);
        $this->assertSame(0, CoaTransaction::count());

        $controller->update(Request::create('/', 'PUT', [...$data, 'status' => 'terbit']), $invoice);
        $controller->destroy($invoice);
        $this->assertSame('0.00', $kas->fresh()->saldo);
        $this->assertSame('0.00', $biaya->fresh()->saldo);
        $this->assertSame(0, CoaTransaction::count());
        $this->assertSame(0, InvoiceKasPinang::count());
    }
}
