<?php

namespace Tests\Feature;

use App\Models\Karyawan;
use App\Services\KaryawanBpjsTextUpdater;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class KaryawanBpjsTextUpdaterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            foreach (['nik', 'nama_lengkap', 'dpp_jkn', 'group_jkn', 'dpp_bp_jamsostek', 'group_bp_jamsostek', 'cabang_bpjs'] as $column) {
                $table->string($column)->nullable();
            }
            $table->timestamps();
        });
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('auditable_id');
            foreach (['user_name', 'auditable_type', 'action', 'module', 'description', 'ip_address', 'user_agent', 'url'] as $column) {
                $table->string($column)->nullable();
            }
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->timestamp('created_at')->nullable();
        });
        DB::table('karyawans')->insert([
            'nik' => '01593', 'nama_lengkap' => 'Aditya Nur Rahmat Kaya',
            'dpp_jkn' => '1000000', 'group_jkn' => 'JKN-LAMA',
            'dpp_bp_jamsostek' => '2000000', 'group_bp_jamsostek' => 'PPU-LAMA',
            'cabang_bpjs' => 'Batam',
        ]);
    }

    private function updateText(string $text): array
    {
        return app(KaryawanBpjsTextUpdater::class)->update($text);
    }

    public function test_both_pairs_are_updated_and_audited_with_indonesian_amounts(): void
    {
        $result = $this->updateText('nik;01593, nama;aditya nur rahmat kaya, DPP JKN;3.200.000, Group JKN;JKN-KIS-KANTOR, DPP BP Jamsostek;4.100.000,50, Group BP Jamsostek;PPU-KANTOR, Cabang BPJS;Jakarta');
        $this->assertSame(1, $result['updated']);
        $this->assertSame([], $result['errors']);
        $employee = Karyawan::first();
        $this->assertSame('3200000', $employee->dpp_jkn);
        $this->assertSame('4100000.50', $employee->dpp_bp_jamsostek);
        $this->assertSame('JKN-KIS-KANTOR', $employee->group_jkn);
        $this->assertSame('PPU-KANTOR', $employee->group_bp_jamsostek);
        $this->assertSame('Jakarta', $employee->cabang_bpjs);
        $this->assertSame(1, DB::table('audit_logs')->where('action', 'updated')->count());
    }

    public function test_one_pair_and_blank_fields_preserve_other_values_and_reimport_is_unchanged(): void
    {
        $line = 'nik;01593, nama;aditya nur rahmat kaya, DPP JKN;3200000, Group JKN;JKN-KIS-KANTOR, DPP BP Jamsostek;, Group BP Jamsostek;, Cabang BPJS;';
        $this->assertSame(1, $this->updateText($line)['updated']);
        $employee = Karyawan::first();
        $this->assertSame('2000000', $employee->dpp_bp_jamsostek);
        $this->assertSame('PPU-LAMA', $employee->group_bp_jamsostek);
        $this->assertSame('Batam', $employee->cabang_bpjs);
        $this->assertSame(1, $this->updateText($line)['unchanged']);
        $this->assertSame(1, DB::table('audit_logs')->count());
    }

    public function test_jamsostek_only_and_zero_amount_are_supported(): void
    {
        $result = $this->updateText('nik;01593, nama;aditya nur rahmat kaya, DPP BP Jamsostek;0, Group BP Jamsostek;PPU-KANTOR');
        $this->assertSame(1, $result['updated']);
        $this->assertSame('0', Karyawan::first()->dpp_bp_jamsostek);
        $this->assertSame('1000000', Karyawan::first()->dpp_jkn);
        $this->assertSame('JKN-LAMA', Karyawan::first()->group_jkn);
    }

    public function test_misspelled_groups_are_rejected_without_partially_updating_dpp(): void
    {
        foreach (['Group JKN;JKN-KISS-KANTOR', 'Group BP Jamsostek;PPU-KANTOR-SALAH'] as $group) {
            $result = $this->updateText('nik;01593, nama;Aditya Nur Rahmat Kaya, DPP JKN;5200000, '.$group);
            $this->assertSame(0, $result['updated']);
            $this->assertCount(1, $result['errors']);
            $this->assertStringContainsString('tidak tersedia', $result['errors'][0]);
        }
        $this->assertSame('1000000', Karyawan::first()->dpp_jkn);
        $this->assertSame('JKN-LAMA', Karyawan::first()->group_jkn);
    }

    public function test_group_case_and_spaces_are_normalized_to_dropdown_values(): void
    {
        $result = $this->updateText('nik;01593, nama;Aditya Nur Rahmat Kaya, Group JKN;jkn-kis-kantor, Group BP Jamsostek;bpu-non   kary-pbm');
        $this->assertSame([], $result['errors']);
        $this->assertSame('JKN-KIS-KANTOR', Karyawan::first()->group_jkn);
        $this->assertSame('BPU-NON KARY-PBM', Karyawan::first()->group_bp_jamsostek);
    }

    public function test_missing_or_blank_name_is_rejected_without_changing_data(): void
    {
        foreach (['nik;01593, DPP JKN;3200000', 'nik;01593, nama;   , DPP JKN;3200000'] as $line) {
            $result = $this->updateText($line);
            $this->assertSame(0, $result['updated']);
            $this->assertSame(['Baris 1: Nama karyawan wajib diisi.'], $result['errors']);
            $this->assertSame([$line], $result['failed_text']);
        }
        $this->assertSame('1000000', Karyawan::first()->dpp_jkn);
        $this->assertSame(0, DB::table('audit_logs')->count());
    }

    public function test_invalid_rows_do_not_partially_change_employee_and_valid_rows_continue(): void
    {
        $invalid = [
            'nik;01593, nama;aditya nur rahmat kaya, DPP JKN;3200000, DPP BP Jamsostek;PPU-KANTOR',
            'nik;01593, nama;Orang Lain, Cabang BPJS;Jakarta',
            'nik;99999, nama;aditya nur rahmat kaya, Cabang BPJS;Jakarta',
            'nama;Aditya, Cabang BPJS;Jakarta',
            'nik;01593, nama;aditya nur rahmat kaya, DPP JKN;3.20.000',
            'nik;01593, nama;aditya nur rahmat kaya, DPP JKN;-1',
            'nik;01593, nama;aditya nur rahmat kaya, DPP JKN;100, DPP JKN;200',
            'nik;01593, nama;aditya nur rahmat kaya, DPP JKN;100, tidak dikenal;abc',
            'nik;01593, nama;aditya nur rahmat kaya, DPP JKN;',
            '1593;3200000',
        ];
        $result = $this->updateText(implode("\r\n", [...$invalid, '', 'nik;01593, nama;aditya nur rahmat kaya, Cabang BPJS;Jakarta']));
        $this->assertSame(1, $result['updated']);
        $this->assertCount(count($invalid), $result['errors']);
        $this->assertSame($invalid, $result['failed_text']);
        $this->assertSame('1000000', Karyawan::first()->dpp_jkn);
        $this->assertSame('2000000', Karyawan::first()->dpp_bp_jamsostek);
        $this->assertSame('Jakarta', Karyawan::first()->cabang_bpjs);
    }
}
