<?php

namespace Tests\Feature;

use App\Http\Controllers\UangJalanApprovalController;
use App\Http\Controllers\UangJalanController;
use App\Models\UangJalanApprovalRequest;
use App\Services\UangJalanApprovalService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UangJalanApprovalTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'cache.default' => 'array']);
        DB::purge('sqlite');

        Schema::create('surat_jalans', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat_jalan')->nullable();
            $table->string('supir')->nullable();
            $table->string('no_plat')->nullable();
            $table->date('tanggal_surat_jalan')->nullable();
            $table->string('status')->nullable();
            $table->string('status_pembayaran_uang_jalan')->nullable();
            $table->boolean('is_supir_customer')->nullable();
            $table->timestamps();
        });
        Schema::create('tanda_terimas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('surat_jalan_id');
            $table->string('approval_pelabuhan_status')->nullable();
            $table->timestamps();
        });
        Schema::create('uang_jalans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_uang_jalan')->unique();
            $table->date('tanggal_uang_jalan');
            $table->unsignedBigInteger('surat_jalan_id')->nullable();
            $table->unsignedBigInteger('surat_jalan_bongkaran_id')->nullable();
            $table->string('kegiatan_bongkar_muat');
            foreach (['jumlah_uang_jalan', 'jumlah_mel', 'jumlah_pelancar', 'jumlah_kawalan', 'jumlah_parkir', 'subtotal', 'jumlah_penyesuaian', 'jumlah_total'] as $column) {
                $table->decimal($column, 12, 2)->nullable();
            }
            $table->string('alasan_penyesuaian')->nullable();
            $table->text('memo')->nullable();
            $table->string('status');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
        Schema::create('uang_jalan_approval_requests', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_surat_jalan');
            $table->unsignedBigInteger('surat_jalan_id');
            $table->string('nomor_surat_jalan')->nullable();
            $table->string('supir')->nullable();
            $table->unsignedInteger('jumlah_tugas_berjalan');
            $table->json('data_uang_jalan');
            $table->string('status');
            $table->unsignedBigInteger('requested_by')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('catatan_approval')->nullable();
            $table->unsignedBigInteger('uang_jalan_id')->nullable();
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
            $table->text('description')->nullable();
            $table->text('old_values')->nullable();
            $table->text('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('url')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    private function buatSuratJalan(string $nomor, string $statusPembayaran = 'dibayar'): int
    {
        return DB::table('surat_jalans')->insertGetId([
            'no_surat_jalan' => $nomor,
            'supir' => 'SUPIR A',
            'tanggal_surat_jalan' => today()->subDay()->toDateString(),
            'status' => 'approved',
            'status_pembayaran_uang_jalan' => $statusPembayaran,
        ]);
    }

    private function formData(int $suratJalanId): array
    {
        return [
            'surat_jalan_id' => $suratJalanId,
            'jenis_surat_jalan' => 'biasa',
            'nomor_uang_jalan' => 'UJTEST001',
            'tanggal_uang_jalan' => today()->toDateString(),
            'kegiatan_bongkar_muat' => 'muat',
            'jumlah_uang_jalan' => 100000,
            'subtotal' => 100000,
            'jumlah_total' => 100000,
        ];
    }

    public function test_two_unfinished_tasks_require_approval_and_approval_creates_money_once(): void
    {
        $this->buatSuratJalan('SJ-1');
        $this->buatSuratJalan('SJ-2');
        $target = $this->buatSuratJalan('SJ-3', 'belum_ada');
        $service = new UangJalanApprovalService;

        app(UangJalanController::class)->store(Request::create('/uang-jalan', 'POST', $this->formData($target)), $service);

        $this->assertSame(0, DB::table('uang_jalans')->count());
        $this->assertDatabaseHas('uang_jalan_approval_requests', [
            'surat_jalan_id' => $target, 'status' => 'pending', 'jumlah_tugas_berjalan' => 2,
        ]);

        $pengajuan = UangJalanApprovalRequest::firstOrFail();
        app(UangJalanApprovalController::class)->approve(Request::create('/approval', 'POST'), $pengajuan, $service);

        $this->assertSame(1, DB::table('uang_jalans')->count());
        $this->assertDatabaseHas('surat_jalans', ['id' => $target, 'status_pembayaran_uang_jalan' => 'sudah_masuk_uang_jalan']);
        $this->assertDatabaseHas('uang_jalan_approval_requests', ['id' => $pengajuan->id, 'status' => 'approved']);
    }

    public function test_completed_task_does_not_count_toward_approval_threshold(): void
    {
        $first = $this->buatSuratJalan('SJ-1');
        $this->buatSuratJalan('SJ-2');
        DB::table('tanda_terimas')->insert([
            'surat_jalan_id' => $first,
            'approval_pelabuhan_status' => 'approved',
            'created_at' => now(),
        ]);
        $target = $this->buatSuratJalan('SJ-3', 'belum_ada');

        app(UangJalanController::class)->store(Request::create('/uang-jalan', 'POST', $this->formData($target)), new UangJalanApprovalService);

        $this->assertSame(0, DB::table('uang_jalan_approval_requests')->count());
        $this->assertDatabaseHas('uang_jalans', ['surat_jalan_id' => $target]);
    }

    public function test_pending_request_is_cancelled_when_previous_tasks_are_completed(): void
    {
        $first = $this->buatSuratJalan('SJ-1');
        $this->buatSuratJalan('SJ-2');
        $target = $this->buatSuratJalan('SJ-3', 'belum_ada');
        $service = new UangJalanApprovalService;
        $controller = app(UangJalanController::class);
        $controller->store(Request::create('/uang-jalan', 'POST', $this->formData($target)), $service);

        DB::table('tanda_terimas')->insert([
            'surat_jalan_id' => $first,
            'approval_pelabuhan_status' => 'approved',
            'created_at' => now(),
        ]);
        $controller->store(Request::create('/uang-jalan', 'POST', $this->formData($target)), $service);
        $this->assertSame(1, DB::table('uang_jalans')->count());
        $this->assertSame(1, DB::table('uang_jalan_approval_requests')->count());
        $this->assertDatabaseHas('uang_jalan_approval_requests', ['surat_jalan_id' => $target, 'status' => 'cancelled']);
    }

    public function test_rejection_allows_new_request(): void
    {
        $this->buatSuratJalan('SJ-1');
        $this->buatSuratJalan('SJ-2');
        $target = $this->buatSuratJalan('SJ-3', 'belum_ada');
        $service = new UangJalanApprovalService;
        $controller = app(UangJalanController::class);
        $controller->store(Request::create('/uang-jalan', 'POST', $this->formData($target)), $service);

        $pengajuan = UangJalanApprovalRequest::firstOrFail();
        app(UangJalanApprovalController::class)->reject(Request::create('/approval', 'POST', [
            'catatan_approval' => 'Selesaikan tugas sebelumnya.',
        ]), $pengajuan);
        $this->assertDatabaseHas('uang_jalan_approval_requests', ['id' => $pengajuan->id, 'status' => 'rejected']);

        $controller->store(Request::create('/uang-jalan', 'POST', $this->formData($target)), $service);
        $this->assertSame(2, DB::table('uang_jalan_approval_requests')->count());
        $this->assertSame(0, DB::table('uang_jalans')->count());
    }
}
