<?php

namespace Tests\Feature;

use App\Http\Controllers\ApprovalTandaTerimaDuaController;
use App\Models\Manifest;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ApprovalTandaTerimaDuaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'cache.default' => 'array']);
        DB::purge('sqlite');

        Schema::create('shipper_consignees', function (Blueprint $table) {
            $table->id();
            $table->string('shipper');
            $table->string('consignee')->nullable();
            $table->text('alamat_shipper')->nullable();
            $table->text('alamat_consignee')->nullable();
            $table->string('notify_party_consignee')->nullable();
            $table->text('alamat_notify_party_consignee')->nullable();
        });
        Schema::create('tanda_terimas', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat_jalan');
            $table->string('notify_party')->nullable();
            $table->text('alamat_notify_party')->nullable();
            $table->unsignedBigInteger('shipper_jb_id')->nullable();
            $table->timestamps();
        });
        Schema::create('tanda_terimas_lcl', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_tanda_terima')->nullable();
            $table->string('nama_pengirim')->nullable();
            $table->unsignedBigInteger('shipper_jb_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('tanda_terima_tanpa_surat_jalan', function (Blueprint $table) {
            $table->id();
            $table->string('no_tanda_terima')->nullable();
            $table->string('nomor_tanda_terima')->nullable();
            $table->string('pengirim')->nullable();
            $table->unsignedBigInteger('shipper_jb_id')->nullable();
            $table->timestamps();
        });
        Schema::create('prospek', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tanda_terima_id')->nullable();
        });
        Schema::create('manifests', function (Blueprint $table) {
            $table->id();
            $table->string('no_voyage');
            $table->string('nomor_tanda_terima')->nullable();
            $table->unsignedBigInteger('prospek_id')->nullable();
            $table->unsignedBigInteger('shipper_id')->nullable();
            $table->unsignedBigInteger('shipper_jb_id')->nullable();
            $table->string('pengirim')->nullable();
            $table->text('alamat_pengirim')->nullable();
            $table->string('penerima')->nullable();
            $table->text('alamat_penerima')->nullable();
            $table->string('notify_party')->nullable();
            $table->text('alamat_notify_party')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->string('auditable_type');
            $table->unsignedBigInteger('auditable_id');
            $table->string('action');
            $table->string('module');
            $table->text('description')->nullable();
            $table->text('old_values')->nullable();
            $table->text('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('url')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function test_shipper_selection_updates_linked_jb_manifests_and_future_manifest_without_changing_existing_shipper_id(): void
    {
        $shipperId = DB::table('shipper_consignees')->insertGetId([
            'shipper' => 'PT Pengirim JB',
            'alamat_shipper' => 'Batam',
            'consignee' => 'PT Penerima JB',
            'notify_party_consignee' => 'PT Notify JB',
        ]);
        $tandaTerimaId = DB::table('tanda_terimas')->insertGetId([
            'no_surat_jalan' => 'SJ-JB-001', 'notify_party' => 'PT Notify TT',
        ]);
        $prospekId = DB::table('prospek')->insertGetId(['tanda_terima_id' => $tandaTerimaId]);
        $jbId = DB::table('manifests')->insertGetId([
            'no_voyage' => 'SA16JB26', 'nomor_tanda_terima' => 'SJ-JB-001',
            'prospek_id' => $prospekId, 'shipper_id' => 77,
        ]);
        $nonJbId = DB::table('manifests')->insertGetId([
            'no_voyage' => 'SA16BJ26', 'nomor_tanda_terima' => 'SJ-JB-001',
            'prospek_id' => $prospekId,
        ]);

        (new ApprovalTandaTerimaDuaController)->update(
            Request::create('/approval-tanda-terima-2/fcl/'.$tandaTerimaId, 'PUT', ['shipper_jb_id' => $shipperId]),
            'fcl', $tandaTerimaId
        );

        $this->assertEquals($shipperId, DB::table('tanda_terimas')->where('id', $tandaTerimaId)->value('shipper_jb_id'));
        $this->assertEquals($shipperId, DB::table('manifests')->where('id', $jbId)->value('shipper_jb_id'));
        $this->assertEquals(77, DB::table('manifests')->where('id', $jbId)->value('shipper_id'));
        $this->assertEquals('PT Pengirim JB', DB::table('manifests')->where('id', $jbId)->value('pengirim'));
        $this->assertNull(DB::table('manifests')->where('id', $nonJbId)->value('shipper_jb_id'));

        $newManifest = Manifest::create(['no_voyage' => 'SA17JB26', 'nomor_tanda_terima' => 'SJ-JB-001']);
        $this->assertEquals($shipperId, $newManifest->shipper_jb_id);
        $this->assertEquals('PT Pengirim JB', $newManifest->pengirim);
    }

    public function test_lcl_selection_matches_its_receipt_and_an_unnumbered_receipt_cannot_update_unrelated_manifests(): void
    {
        $shipperId = DB::table('shipper_consignees')->insertGetId([
            'shipper' => 'PT LCL JB', 'notify_party_consignee' => 'PT Notify LCL',
        ]);
        $lclId = DB::table('tanda_terimas_lcl')->insertGetId(['nomor_tanda_terima' => 'TT-LCL-1']);
        $ttsjId = DB::table('tanda_terima_tanpa_surat_jalan')->insertGetId([]);
        $linkedManifestId = DB::table('manifests')->insertGetId([
            'no_voyage' => 'SA16JB26', 'nomor_tanda_terima' => 'TT-LCL-1',
        ]);
        $otherManifestId = DB::table('manifests')->insertGetId([
            'no_voyage' => 'SA16JB26', 'nomor_tanda_terima' => 'TT-LCL-2',
        ]);

        $controller = new ApprovalTandaTerimaDuaController;
        $controller->update(Request::create('/approval-tanda-terima-2/lcl/'.$lclId, 'PUT', ['shipper_jb_id' => $shipperId]), 'lcl', $lclId);
        $controller->update(Request::create('/approval-tanda-terima-2/ttsj/'.$ttsjId, 'PUT', ['shipper_jb_id' => $shipperId]), 'ttsj', $ttsjId);

        $this->assertEquals($shipperId, DB::table('manifests')->where('id', $linkedManifestId)->value('shipper_jb_id'));
        $this->assertNull(DB::table('manifests')->where('id', $otherManifestId)->value('shipper_jb_id'));
        $this->assertEquals($shipperId, DB::table('tanda_terima_tanpa_surat_jalan')->where('id', $ttsjId)->value('shipper_jb_id'));
    }
}
