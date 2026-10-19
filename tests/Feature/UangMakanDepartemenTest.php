<?php

namespace Tests\Feature;

use App\Http\Controllers\UangMakanController;
use App\Models\Karyawan;
use App\Models\KaryawanTidakTetap;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UangMakanDepartemenTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');

        foreach (['karyawans', 'karyawan_tidak_tetaps'] as $name) {
            Schema::create($name, function (Blueprint $table) use ($name) {
                $table->id();
                $table->string('nama_lengkap');
                $table->string('nik');
                $table->string('penempatan')->nullable();
                if ($name === 'karyawans') {
                    $table->string('departemen')->nullable();
                }
            });
        }
        Schema::create('uang_makans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('karyawan_id');
            $table->string('tipe_karyawan');
            $table->date('tanggal');
            $table->decimal('nominal');
            $table->timestamps();
        });

        foreach (['JAKARTA KANTOR', 'BATAM', null, '', '   ', ' JAKARTA KANTOR '] as $index => $departemen) {
            DB::table('karyawans')->insert([
                'id' => $index + 1, 'nama_lengkap' => 'Karyawan '.($index + 1),
                'nik' => 'NIK'.($index + 1), 'penempatan' => 'KANTOR', 'departemen' => $departemen,
            ]);
            $this->insertUangMakan($index + 1, Karyawan::class);
        }
        DB::table('karyawan_tidak_tetaps')->insert([
            'id' => 1, 'nama_lengkap' => 'Harian', 'nik' => 'H001', 'penempatan' => 'KANTOR',
        ]);
        $this->insertUangMakan(1, KaryawanTidakTetap::class);
        $this->insertUangMakan(999, Karyawan::class);
    }

    private function insertUangMakan(int $karyawanId, string $type): void
    {
        DB::table('uang_makans')->insert([
            'karyawan_id' => $karyawanId, 'tipe_karyawan' => $type,
            'tanggal' => '2026-10-19', 'nominal' => 25000,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function indexData(array $filters = []): array
    {
        return (new UangMakanController)->index(Request::create('/uang-makan', 'GET', $filters))->getData();
    }

    public function test_groups_departments_and_places_missing_departments_last(): void
    {
        $data = $this->indexData();

        $this->assertSame(['BATAM', 'JAKARTA KANTOR', 'BLANK'], $data['uangMakansPerDepartemen']->keys()->all());
        $this->assertCount(2, $data['uangMakansPerDepartemen']['JAKARTA KANTOR']);
        $this->assertCount(5, $data['uangMakansPerDepartemen']['BLANK']);
        $this->assertSame(['BATAM', 'JAKARTA KANTOR'], $data['departemens']->all());
    }

    public function test_department_filter_only_includes_employees_from_that_department(): void
    {
        $data = $this->indexData(['departemen' => 'JAKARTA KANTOR']);

        $this->assertEqualsCanonicalizing([1, 6], $data['uangMakans']->pluck('karyawan_id')->all());
        $this->assertSame(['JAKARTA KANTOR'], $data['uangMakansPerDepartemen']->keys()->all());
    }

    public function test_blank_filter_includes_null_empty_whitespace_temporary_and_missing_employees(): void
    {
        $data = $this->indexData(['departemen' => '__blank__']);

        $this->assertSame(5, $data['uangMakans']->total());
        $this->assertSame(['BLANK'], $data['uangMakansPerDepartemen']->keys()->all());
    }

    public function test_department_filter_combines_with_search_for_both_employee_types(): void
    {
        $data = $this->indexData(['departemen' => 'JAKARTA KANTOR', 'search' => 'NIK6']);
        $this->assertSame([6], $data['uangMakans']->pluck('karyawan_id')->all());

        $data = $this->indexData(['departemen' => '__blank__', 'search' => 'Harian']);
        $this->assertSame([KaryawanTidakTetap::class], $data['uangMakans']->pluck('tipe_karyawan')->all());

        $data = $this->indexData(['departemen' => 'TIDAK ADA']);
        $this->assertSame(0, $data['uangMakans']->total());
        $this->assertCount(0, $data['uangMakansPerDepartemen']);
    }

    public function test_view_renders_separate_department_tables_and_independent_delete_forms(): void
    {
        $this->be((new User)->forceFill(['id' => 1, 'username' => 'kiky']));
        // Render the page content without the unrelated application sidebar/layout.
        $template = str_replace(
            ["@extends('layouts.app')", "@section('content')", '@endsection'],
            '',
            file_get_contents(resource_path('views/uang-makan/index.blade.php'))
        );
        $html = Blade::render($template, $this->indexData());
        $dom = new \DOMDocument;
        @$dom->loadHTML('<?xml encoding="UTF-8">'.$html);
        $xpath = new \DOMXPath($dom);

        $this->assertSame(3, $xpath->query('//section/table | //section/div/table')->length);
        $this->assertSame(['BATAM', 'JAKARTA KANTOR', 'BLANK'], array_map(
            fn ($node) => trim($node->textContent), iterator_to_array($xpath->query('//section/div/h2'))
        ));
        $this->assertSame(8, $xpath->query('//input[@name="ids[]" and @form="bulk-delete-form"]')->length);
        $this->assertSame(8, $xpath->query('//td/form')->length);
        $this->assertSame(0, $xpath->query('//form//form')->length);
        $this->assertStringContainsString('BLANK (Tanpa Departemen)', $html);
    }
}
