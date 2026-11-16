<?php

namespace Tests\Feature;

use App\Http\Controllers\PranotaBpjsController;
use App\Models\Karyawan;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PranotaBpjsEmployeeChangesTest extends TestCase
{
    public function test_changes_include_new_employees_and_termination_updates_without_unrelated_updates(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            foreach (['nik', 'nama_lengkap', 'jkn', 'no_ketenagakerjaan', 'status_jkn', 'status_bp_jamsostek', 'tipe_jkn', 'tipe_bp_jamsostek', 'group_jkn', 'group_bp_jamsostek', 'dpp_jkn', 'dpp_bp_jamsostek', 'cabang_bpjs'] as $field) {
                $table->string($field)->nullable();
            }
            $table->timestamps();
            $table->date('tanggal_berhenti')->nullable();
        });
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('auditable_type');
            $table->unsignedBigInteger('auditable_id');
            $table->string('action');
            $table->json('old_values');
            $table->json('new_values');
            $table->timestamp('created_at');
        });
        foreach ([1 => null, 2 => '2026-10-01', 3 => '2026-11-05', 4 => '2026-09-01'] as $id => $stop) {
            DB::table('karyawans')->insert(['id' => $id, 'nama_lengkap' => 'Karyawan '.$id, 'nik' => '00'.$id,
                'created_at' => $id === 1 ? '2026-11-01 00:00:00' : '2026-10-01 00:00:00',
                'tanggal_berhenti' => $stop, 'jkn' => 'JKN-'.$id, 'dpp_jkn' => '5000000']);
        }
        foreach ([2 => ['tanggal_berhenti' => '2026-10-01'], 4 => ['nama_lengkap' => 'Nama baru']] as $id => $changes) {
            DB::table('audit_logs')->insert(['auditable_type' => Karyawan::class, 'auditable_id' => $id,
                'action' => 'updated', 'old_values' => json_encode(['tanggal_berhenti' => null]),
                'new_values' => json_encode($changes), 'created_at' => '2026-11-16 09:00:00']);
        }
        $response = (new PranotaBpjsController)->employeeChanges(Request::create('/', 'GET', ['bulan' => 11, 'tahun' => 2026]));
        $data = $response->getData(true);
        $this->assertSame([1], array_column($data['baru'], 'id'));
        $this->assertSame('001', $data['baru'][0]['nik']);
        $this->assertSame('JKN-1', $data['baru'][0]['jkn']);
        $this->assertSame('5000000', $data['baru'][0]['dpp_jkn']);
        $this->assertSame([2, 3], array_column($data['berhenti'], 'id'));
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
    }
}
