<?php

namespace Tests\Unit;

use App\Services\RitSuratJalanService;
use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\TestCase;

class RitSuratJalanServiceTest extends TestCase
{
    private $previousResolver;

    private Manager $database;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousResolver = Model::getConnectionResolver();
        $this->database = new Manager;
        $this->database->addConnection(['driver' => 'sqlite', 'database' => ':memory:']);
        $this->database->bootEloquent();
        $schema = $this->database->getConnection()->getSchemaBuilder();
        foreach (['surat_jalans', 'surat_jalan_bongkarans'] as $table) {
            $schema->create($table, function ($t) {
                $t->increments('id');
                foreach (['no_surat_jalan', 'nomor_surat_jalan', 'rit', 'status', 'kegiatan', 'tanggal_checkpoint', 'tanggal_tanda_terima', 'tanggal_surat_jalan', 'status_pembayaran_uang_rit'] as $column) {
                    $t->string($column)->nullable();
                }
            });
        }
        $schema->create('tanda_terimas', function ($t) {
            $t->increments('id');
            $t->integer('surat_jalan_id');
            $t->string('tanggal');
        });
        $schema->create('tanda_terima_bongkarans', function ($t) {
            $t->increments('id');
            $t->integer('surat_jalan_bongkaran_id');
            $t->string('tanggal_tanda_terima');
        });
        $schema->create('pranota_uang_rits', function ($t) {
            $t->increments('id');
            $t->integer('surat_jalan_id')->nullable();
            $t->integer('surat_jalan_bongkaran_id')->nullable();
            $t->string('no_surat_jalan')->nullable();
            $t->string('status');
        });
    }

    protected function tearDown(): void
    {
        if ($this->previousResolver) {
            Model::setConnectionResolver($this->previousResolver);
        } else {
            Model::unsetConnectionResolver();
        }
        parent::tearDown();
    }

    public function test_date_range_includes_all_report_sources_and_processed_records(): void
    {
        $db = $this->database->getConnection();
        $db->table('surat_jalans')->insert([
            ['id' => 1, 'rit' => 'menggunakan_rit', 'tanggal_checkpoint' => '2026-10-03 00:00:00', 'status_pembayaran_uang_rit' => 'belum_dibayar'],
            ['id' => 2, 'rit' => 'menggunakan_rit', 'tanggal_checkpoint' => '2026-10-09 23:59:59', 'status_pembayaran_uang_rit' => 'sudah_masuk_pranota'],
            ['id' => 3, 'rit' => 'menggunakan_rit', 'tanggal_checkpoint' => '2026-10-10 00:00:00', 'status_pembayaran_uang_rit' => null],
            ['id' => 4, 'rit' => 'tidak_menggunakan_rit', 'tanggal_checkpoint' => '2026-10-04', 'status_pembayaran_uang_rit' => null],
        ]);
        $db->table('surat_jalans')->insert(['id' => 5, 'rit' => 'menggunakan_rit', 'status' => 'draft', 'kegiatan' => 'bongkaran', 'tanggal_tanda_terima' => '2026-10-05']);
        $db->table('surat_jalans')->insert(['id' => 6, 'rit' => 'menggunakan_rit', 'status' => 'approved', 'tanggal_surat_jalan' => '2026-10-06']);
        $db->table('surat_jalans')->insert(['id' => 7, 'rit' => 'menggunakan_rit']);
        $db->table('tanda_terimas')->insert(['surat_jalan_id' => 7, 'tanggal' => '2026-10-07']);
        $db->table('surat_jalan_bongkarans')->insert(['id' => 1, 'rit' => null, 'status_pembayaran_uang_rit' => 'lunas']);
        $db->table('tanda_terima_bongkarans')->insert(['surat_jalan_bongkaran_id' => 1, 'tanggal_tanda_terima' => '2026-10-09 23:59:59']);

        $service = new RitSuratJalanService;
        $start = Carbon::parse('2026-10-03');
        $end = Carbon::parse('2026-10-09');
        $regular = $service->regular($start, $end)->orderBy('id')->get();
        $bongkaran = $service->bongkaran($start, $end)->get();
        $this->assertSame([1, 2, 5, 6, 7], $regular->pluck('id')->all());
        $this->assertCount(1, $bongkaran);
        $this->assertSame('belum_dibayar', $regular[0]->status_pembayaran_uang_rit);
        $this->assertSame('sudah_masuk_pranota', $regular[1]->status_pembayaran_uang_rit);
        $this->assertSame('lunas', $bongkaran[0]->status_pembayaran_uang_rit);
        $this->assertCount(5, $regular);
    }

    public function test_existing_pranotas_do_not_exclude_surat_jalan_from_selection(): void
    {
        $this->database->getConnection()->table('pranota_uang_rits')->insert([
            ['status' => 'draft', 'surat_jalan_id' => 1, 'no_surat_jalan' => 'SJ-01, SJ-02, SJ-03 (Bongkaran)'],
            ['status' => 'cancelled', 'surat_jalan_id' => 4, 'no_surat_jalan' => 'SJ-04'],
        ]);
        $db = $this->database->getConnection();
        foreach ([1, 2, 4] as $id) {
            $db->table('surat_jalans')->insert(['id' => $id, 'no_surat_jalan' => 'SJ-0'.$id,
                'rit' => 'menggunakan_rit', 'tanggal_checkpoint' => '2026-10-05',
                'status_pembayaran_uang_rit' => 'sudah_masuk_pranota']);
        }
        $db->table('surat_jalan_bongkarans')->insert(['id' => 3, 'nomor_surat_jalan' => 'SJ-03',
            'rit' => 'menggunakan_rit', 'tanggal_checkpoint' => '2026-10-05', 'status_pembayaran_uang_rit' => 'lunas']);
        $service = new RitSuratJalanService;
        $start = Carbon::parse('2026-10-03');
        $end = Carbon::parse('2026-10-09');
        $this->assertSame([1, 2, 4], $service->regular($start, $end)->orderBy('id')->pluck('id')->all());
        $this->assertSame([3], $service->bongkaran($start, $end)->pluck('id')->all());
    }
}
