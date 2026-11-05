<?php

namespace Tests\Feature;

use App\Http\Controllers\SuratJalanBongkaranTanjungPinangController;
use App\Models\SuratJalanBongkaran;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SuratJalanBongkaranTanjungPinangTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('surat_jalan_bongkarans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat_jalan')->unique();
            $table->date('tanggal_surat_jalan');
            $table->string('lokasi')->nullable();
            $table->string('input_by')->nullable();
            $table->dateTime('input_date')->nullable();
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
    }

    public function test_records_are_fixed_to_tanjung_pinang_and_other_locations_are_inaccessible(): void
    {
        $controller = app(SuratJalanBongkaranTanjungPinangController::class);
        $controller->store(Request::create('/', 'POST', [
            'nomor_surat_jalan' => 'SJ-TP-001',
            'tanggal_surat_jalan' => '2026-11-05',
            'lokasi' => 'jakarta',
        ]));

        $tanjungPinang = SuratJalanBongkaran::firstOrFail();
        $this->assertSame('tanjung_pinang', $tanjungPinang->lokasi);

        $jakarta = SuratJalanBongkaran::create([
            'nomor_surat_jalan' => 'SJ-JKT-001',
            'tanggal_surat_jalan' => '2026-11-05',
            'lokasi' => 'jakarta',
        ]);

        $view = $controller->index(Request::create('/'));
        $this->assertSame(1, $view->getData()['suratJalans']->total());

        $controller->update(Request::create('/', 'PUT', [
            'nomor_surat_jalan' => 'SJ-TP-001',
            'tanggal_surat_jalan' => '2026-11-06',
            'lokasi' => 'jakarta',
        ]), $tanjungPinang->id);
        $this->assertSame('tanjung_pinang', $tanjungPinang->fresh()->lokasi);

        $this->expectException(ModelNotFoundException::class);
        $controller->edit($jakarta->id);
    }
}
