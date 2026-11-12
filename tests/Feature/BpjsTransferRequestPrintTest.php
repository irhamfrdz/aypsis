<?php

namespace Tests\Feature;

use App\Models\PembayaranPranotaBpjs;
use App\Models\PranotaBpjsHeader;
use App\Http\Controllers\PembayaranPranotaBpjsController;
use App\Services\CoaTransactionService;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BpjsTransferRequestPrintTest extends TestCase
{
    private function renderPrint(?string $document = null, array $allocations = []): string
    {
        $this->app->instance('request', Request::create('/pembayaran-pranota-bpjs/1/print', 'GET', [
            'dokumen' => $document,
        ]));

        $pranota = (new PranotaBpjsHeader)->forceFill([
            'nomor_pranota' => 'PBPJS26110001', 'periode_bulan' => 11, 'periode_tahun' => 2026,
        ]);
        $pranota->setRelation('pivot', new Pivot(['subtotal' => 125000.50]));
        $payment = (new PembayaranPranotaBpjs)->forceFill([
            'nomor_pembayaran' => 'SIS-11-26-001296', 'tanggal_pembayaran' => '2026-11-12',
            'total_pembayaran' => 125000.50,
            'journal_allocations' => $allocations,
            'bank_allocations' => [['nomor_akun' => '6101.001', 'nama_akun' => 'Gaji Jakarta', 'subtotal' => 125000.50]],
        ]);
        $payment->setRelation('pranotas', collect([$pranota]));
        foreach (['bank', 'akunCoa', 'createdBy'] as $relation) {
            $payment->setRelation($relation, null);
        }

        return view('pembayaran-pranota-bpjs.print', [
            'payment' => $payment,
            'parts' => collect([(object) ['komponen' => 'kesehatan', 'tipe' => 'KANTOR', 'subtotal' => 125000.50]]),
            'journalAmounts' => ['kesehatan|KANTOR' => ['tunjangan' => 0, 'biaya' => 125000.50, 'hutang' => 0]],
            'accountLabels' => ['kesehatan|KANTOR' => '6101.006 - Biaya BPJS Jakarta'],
        ])->render();
    }

    public function test_transfer_request_uses_payment_template_without_type_breakdown(): void
    {
        $html = $this->renderPrint('permohonan-transfer');

        $this->assertStringContainsString('<h1>Permohonan Transfer BPJS</h1>', $html);
        $this->assertStringNotContainsString('Rincian Tipe yang Dibayar', $html);
        $this->assertStringNotContainsString('Total Rincian', $html);
        $this->assertStringNotContainsString('>LUNAS<', $html);
        $this->assertStringContainsString('SIS-11-26-001296', $html);
        $this->assertStringContainsString('PBPJS26110001', $html);
        $this->assertStringContainsString('Rp 125.000,50', $html);
        $this->assertStringContainsString('12/11/2026', $html);
        $this->assertStringContainsString('Disetujui oleh', $html);
        $this->assertStringContainsString('window.print()', $html);
    }

    public function test_regular_receipt_keeps_type_breakdown_and_existing_title(): void
    {
        $html = $this->renderPrint();

        $this->assertStringContainsString('<h1>Pembayaran Pranota BPJS</h1>', $html);
        $this->assertStringContainsString('Rincian Tipe yang Dibayar', $html);
        $this->assertStringContainsString('Total Rincian', $html);
        $this->assertStringContainsString('6101.006 - Biaya BPJS Jakarta', $html);
        $this->assertStringContainsString('>LUNAS<', $html);
    }

    public function test_transfer_request_uses_existing_protected_print_route(): void
    {
        $url = route('pembayaran-pranota-bpjs.print', [1, 'dokumen' => 'permohonan-transfer']);
        $route = app('router')->getRoutes()->match(Request::create($url));

        $this->assertStringContainsString('dokumen=permohonan-transfer', $url);
        $this->assertSame('pembayaran-pranota-bpjs.print', $route->getName());
        $this->assertContains('can:pembayaran-pranota-bpjs-view', $route->gatherMiddleware());
    }

    public function test_both_print_formats_show_cost_and_debt_accounts_in_bank_section(): void
    {
        $allocations = [
            ['nomor_akun' => '2103.002', 'nama_akun' => 'Utang Biaya BPJS', 'subtotal' => 20000.50],
            ['nomor_akun' => '6101.001', 'nama_akun' => 'Gaji Jakarta', 'subtotal' => 25000],
            ['nomor_akun' => '6101.006', 'nama_akun' => 'Biaya BPJS Jakarta', 'subtotal' => 80000],
        ];
        foreach ([null, 'permohonan-transfer'] as $document) {
            $html = $this->renderPrint($document, $allocations);
            $accountsSection = explode('<strong>Bank yang Digunakan</strong>', explode('<strong>Akun Bank</strong>', $html)[1])[0];
            foreach (['6101.001 - Gaji Jakarta', 'Rp 25.000,00', '6101.006 - Biaya BPJS Jakarta', 'Rp 80.000,00', '2103.002 - Utang Biaya BPJS', 'Rp 20.000,50'] as $expected) {
                $this->assertStringContainsString($expected, $accountsSection);
            }
            $this->assertStringNotContainsString('<b>BIAYA</b>', $accountsSection);
            $this->assertStringNotContainsString('<b>HUTANG</b>', $accountsSection);
            $this->assertLessThan(strpos($accountsSection, '6101.006'), strpos($accountsSection, '6101.001'));
            $this->assertLessThan(strpos($accountsSection, '2103.002'), strpos($accountsSection, '6101.006'));
            // Jangan menggandakan total akun asal dengan rincian jurnalnya.
            $this->assertStringNotContainsString('Rp 125.000,50', $accountsSection);
        }
    }

    public function test_old_payments_read_existing_journal_without_changing_it(): void
    {
        Schema::create('akun_coa', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_akun');
            $table->string('nama_akun');
        });
        Schema::create('coa_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coa_id');
            $table->string('nomor_referensi');
            $table->string('jenis_transaksi');
            $table->decimal('debit', 15, 2);
        });
        DB::table('akun_coa')->insert([
            ['id' => 1, 'nomor_akun' => '6201.006', 'nama_akun' => 'Biaya BPJS Batam'],
            ['id' => 2, 'nomor_akun' => '2103.002', 'nama_akun' => 'Utang Biaya BPJS'],
        ]);
        foreach ([[1, 'BPJS-OLD', 30], [1, 'BPJS-OLD', 20], [2, 'BPJS-OLD', 10], [1, 'OTHER', 999]] as [$id, $reference, $amount]) {
            DB::table('coa_transactions')->insert(['coa_id' => $id, 'nomor_referensi' => $reference, 'jenis_transaksi' => 'Pembayaran Pranota BPJS', 'debit' => $amount]);
        }
        $controller = new class(new CoaTransactionService) extends PembayaranPranotaBpjsController
        {
            public function allocations(PembayaranPranotaBpjs $payment): array
            {
                return $this->printAccountAllocations($payment);
            }
        };
        $payment = new PembayaranPranotaBpjs(['nomor_pembayaran' => 'BPJS-OLD']);
        $accounts = collect($controller->allocations($payment))->keyBy('nomor_akun');
        $this->assertCount(2, $accounts);
        $this->assertEquals(50, $accounts['6201.006']['subtotal']);
        $this->assertEquals(10, $accounts['2103.002']['subtotal']);
        $this->assertSame(4, DB::table('coa_transactions')->count());

        $payment->journal_allocations = [['nomor_akun' => '6201.006', 'nama_akun' => 'Biaya BPJS Batam', 'subtotal' => 70]];
        $this->assertSame($payment->journal_allocations, $controller->allocations($payment));

        $payment->nomor_pembayaran = 'NO-JOURNAL';
        $payment->journal_allocations = null;
        $payment->bank_allocations = [['nomor_akun' => '6101.001', 'nama_akun' => 'Gaji Jakarta', 'subtotal' => 100]];
        $this->assertSame($payment->bank_allocations, $controller->allocations($payment));
    }
}
