<?php

namespace Tests\Feature;

use App\Http\Controllers\PranotaBpjsController;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PranotaBpjsListViewTest extends TestCase
{
    private bool $allowActions = true;

    protected function setUp(): void
    {
        parent::setUp();
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username');
        });
        (require database_path('migrations/2026_08_28_112737_create_pranota_bpjs_headers_table.php'))->up();
        DB::table('users')->insert([
            ['id' => 1, 'name' => 'Finance Satu', 'username' => 'Finance Satu'],
            ['id' => 2, 'name' => 'HRD Dua', 'username' => 'HRD Dua'],
        ]);
        foreach ([[1, 'draft', 11, 2026, 100.50], [2, 'partial', 10, 2026, 200.75], [3, 'paid', 11, 2025, 300]] as [$id, $status, $month, $year, $total]) {
            DB::table('pranota_bpjs_headers')->insert([
                'id' => $id, 'nomor_pranota' => 'PBPJS-TEST-'.$id,
                'tanggal_pranota' => $year.'-'.str_pad($month, 2, '0', STR_PAD_LEFT).'-12',
                'periode_bulan' => $month, 'periode_tahun' => $year, 'status' => $status,
                'total_karyawan' => 10, 'total_bpjs_kesehatan' => 40,
                'total_bpjs_ketenagakerjaan' => $total - 40, 'grand_total' => $total,
                'created_by' => $id === 1 ? 1 : 2, 'created_at' => '2026-11-12 10:29:00',
                'keterangan' => $id === 1 ? 'Tim kantor' : 'Tim lapangan',
            ]);
        }
        Gate::before(fn (?User $user) => $this->allowActions);
    }

    private function render(array $parameters = [], bool $history = false): array
    {
        $request = Request::create('/pranota-bpjs', 'GET', $parameters);
        $this->app->instance('request', $request);
        $data = (new PranotaBpjsController)->index($request)->getData();
        $data['isHistory'] = $history;
        // Render this page in isolation so permission assertions do not depend on the global sidebar.
        view()->flushState();
        $source = str_replace("@extends('layouts.app')", '', file_get_contents(resource_path('views/pranota-bpjs/index.blade.php')));
        $html = Blade::render($source."\n@yield('content')\n@stack('styles')", $data);
        $dom = new \DOMDocument;
        @$dom->loadHTML('<?xml encoding="UTF-8">'.$html);

        return [$html, new \DOMXPath($dom), $data];
    }

    public function test_summary_table_and_permission_based_actions_use_existing_data(): void
    {
        [$html, $xpath, $data] = $this->render();
        $this->assertEquals(601.25, $data['totalNominal']);
        $this->assertSame(1, $data['draftCount']);
        $this->assertSame(3, $data['pranotas']->total());
        $this->assertSame(11, $xpath->query('//table[contains(@class,"bpjs-list-table")]/thead/tr/th')->length);
        $this->assertSame(3, $xpath->query('//table[contains(@class,"bpjs-list-table")]/tbody/tr')->length);
        $this->assertSame(3, $xpath->query('//a[@title="Export Excel"]')->length);
        $this->assertSame(3, $xpath->query('//a[@title="Detail"]')->length);
        $this->assertSame(1, $xpath->query('//a[@title="Bayar Pranota"]')->length);
        $this->assertSame(2, $xpath->query('//a[@title="Edit"]')->length);
        $this->assertSame(1, $xpath->query('//button[@title="Hapus"]')->length);
        $this->assertStringContainsString('confirm(', $xpath->query('//button[@title="Hapus"]/ancestor::form')->item(0)->getAttribute('onsubmit'));
        $this->assertStringContainsString('Rp 601,25', $html);
        $this->assertStringContainsString('12/11/2026 10:29', $html);
        foreach (['Partial', 'Draft', 'Lunas'] as $status) {
            $this->assertStringContainsString($status, $html);
        }
        $this->assertStringContainsString(route('pranota-bpjs.history'), $html);
        $this->assertStringContainsString(route('pranota-bpjs.create'), $html);
        $this->assertStringContainsString('css/pranota-bpjs-list.css?v=', $html);
    }

    public function test_search_status_month_year_filters_and_selected_values_are_preserved(): void
    {
        [$html, $xpath, $data] = $this->render(['search' => 'Tim kantor', 'status' => 'draft', 'bulan' => 11, 'tahun' => 2026]);
        $this->assertSame(1, $data['pranotas']->total());
        $this->assertEquals(100.50, $data['totalNominal']);
        $this->assertSame(1, $data['draftCount']);
        $this->assertSame('Tim kantor', $xpath->query('//input[@name="search"]')->item(0)->getAttribute('value'));
        $this->assertSame('2026', $xpath->query('//input[@name="tahun"]')->item(0)->getAttribute('value'));
        $this->assertSame('draft', $xpath->query('//select[@name="status"]/option[@selected]')->item(0)->getAttribute('value'));
        $this->assertSame('11', $xpath->query('//select[@name="bulan"]/option[@selected]')->item(0)->getAttribute('value'));
        $this->assertSame(route('pranota-bpjs.index'), $xpath->query('//input[@name="search"]/ancestor::form')->item(0)->getAttribute('action'));
        [$html, , $data] = $this->render(['search' => 'Finance Satu']);
        $this->assertSame(1, $data['pranotas']->total());
        $this->assertStringContainsString('PBPJS-TEST-1', $html);
    }

    public function test_denied_actions_are_hidden_and_empty_state_is_preserved(): void
    {
        $this->allowActions = false;
        [, $xpath] = $this->render();
        foreach (['Export Excel', 'Detail', 'Edit', 'Bayar Pranota'] as $title) {
            $this->assertSame(0, $xpath->query('//a[@title="'.$title.'"]')->length);
        }
        $this->assertSame(0, $xpath->query('//button[@title="Hapus"]')->length);
        [$html, , $data] = $this->render(['status' => 'unknown']);
        $this->assertSame(0, $data['pranotas']->total());
        $this->assertEquals(0, $data['totalNominal']);
        $this->assertStringContainsString('Belum ada data Pranota BPJS', $html);
    }

    public function test_shared_history_page_does_not_opt_in_to_the_new_styles(): void
    {
        [$html, $xpath] = $this->render([], true);
        $this->assertStringNotContainsString('css/pranota-bpjs-list.css?v=', $html);
        $this->assertSame(0, $xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " bpjs-pranota-list ")]')->length);
        $this->assertSame(route('pranota-bpjs.history'), $xpath->query('//input[@name="search"]/ancestor::form')->item(0)->getAttribute('action'));
    }
}
